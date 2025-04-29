<?php
session_start();
require_once '../constants.php';
require '../db.php';

$error = '';
$success = '';

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "
    SELECT 
        user.first_name AS firstname, 
        user.last_name AS lastname, 
        user.email, 
        user.contact_no AS contactnumber, 
        user.profile_photo, 
        parent.nic
    FROM parent
    JOIN user ON parent.user_id = user.user_id
    WHERE parent.user_id = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $error = "No profile found for the logged-in user.";
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $contactnumber = trim($_POST['contactnumber']);
    $email = trim($_POST['email']);
    $nic = trim($_POST['nic']);
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $profilePhoto = $row['profile_photo']; // Default to existing profile photo

    // Validate NIC
    if (!ctype_digit($nic)) {
        $error = "NIC must contain only digits.";
    }
    // Validate contact number
    elseif (!ctype_digit($contactnumber) || strlen($contactnumber) !== 10) {
        $error = "Contact number must be exactly 10 digits.";
    }
    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $error = "Email must be a valid Gmail address.";
    }
    // Validate passwords
    elseif (!empty($password) || !empty($repassword)) {
        if ($password !== $repassword) {
            $error = "Passwords do not match.";
        }
    }

    // If no error so far, handle profile photo
    if (empty($error) && isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/profile_photos/';
        $fileTmpPath = $_FILES['profilePhoto']['tmp_name'];
        $fileName = basename($_FILES['profilePhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            $error = "Unsupported image file type.";
        } else {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
            $targetFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $profilePhoto = 'assets/images/profile_photos/' . $newFileName;
            } else {
                $error = "Failed to move uploaded profile photo.";
            }
        }
    }

    // If still no error, update database
    if (empty($error)) {
        $query = "
            UPDATE user
            JOIN parent ON user.user_id = parent.user_id
            SET user.first_name = :firstname,
                user.last_name = :lastname,
                user.email = :email,
                user.contact_no = :contactnumber,
                user.profile_photo = :profilePhoto,
                parent.nic = :nic
        ";
        if (!empty($password)) {
            $query .= ", user.password = :password";
        }
        $query .= " WHERE parent.user_id = :user_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':contactnumber', $contactnumber, PDO::PARAM_STR);
        $stmt->bindParam(':profilePhoto', $profilePhoto, PDO::PARAM_STR);
        $stmt->bindParam(':nic', $nic, PDO::PARAM_STR);
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        }
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $success = "Profile updated successfully!";
        // Update the displayed values after successful update
        $row['firstname'] = $firstname;
        $row['lastname'] = $lastname;
        $row['contactnumber'] = $contactnumber;
        $row['email'] = $email;
        $row['nic'] = $nic;
        $row['profile_photo'] = $profilePhoto;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/editprofile.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <div class="main-layout">
        <?php include __DIR__ . '/sidebar1_parent.php'; ?>

        <main class="main-content">
            <h2 align="center">Edit Profile</h2>

            <?php if (!empty($error)): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif (!empty($success)): ?>
                <div style="color: green; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="photo-frame">
                    <div class="photo-container">
                        <img  
                            src="<?php echo !empty($row['profile_photo']) ? '../../' . htmlspecialchars($row['profile_photo']) : '../../assets/images/default_profile.png'; ?>" 
                            alt="Profile Photo" 
                            id="profilePhotoPreview" 
                            class="profile-photo">
                        <label for="profilePhoto" class="add-photo-icon">+</label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewPhoto(event)" style="display: none;">
                    </div>
                </div>

                <div class="profile-box">
                    <label for="firstname"><strong>First Name:</strong></label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                </div>

                <div class="profile-box">
                    <label for="lastname"><strong>Last Name:</strong></label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                </div>

                <div class="profile-box">
                    <label for="contactnumber"><strong>Contact Number:</strong></label>
                    <input type="text" id="contactnumber" name="contactnumber" value="<?php echo htmlspecialchars($row['contactnumber']); ?>" pattern="\d{10}" title="Contact number must be 10 digits" required>
                </div>

                <div class="profile-box">
                    <label for="email"><strong>E-mail:</strong></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" pattern="[a-z0-9._%+-]+@gmail\.com$" required>
                </div>

                <div class="profile-box">
                    <label for="nic"><strong>NIC:</strong></label>
                    <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($row['nic']); ?>" required>
                </div>

                <div class="profile-box">
                    <label for="password"><strong>New Password:</strong></label>
                    <input type="password" id="password" name="password" placeholder="Enter new password">
                </div>

                <div class="profile-box">
                    <label for="repassword"><strong>Re-enter Password:</strong></label>
                    <input type="password" id="repassword" name="repassword" placeholder="Confirm new password">
                </div>

                <div class="button-container">
                    <button type="submit" class="edit-button">Save</button>
                    <button type="button" class="edit-button cancel-button" onclick="window.location.href='parentprofilepage.php'">Cancel</button>
                </div>
            </form>
        </main>
    </div>

    <?php include __DIR__ . '/../footer.php'; ?>

    <script>
        function previewPhoto(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('profilePhotoPreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>

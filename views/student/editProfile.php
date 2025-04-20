<!-- filepath: c:\xampp\htdocs\eduburd\views\student\editProfile.php -->
<?php 
session_start();
require '../db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Retrieve user data from the database
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, contact_no, profile_photo FROM user WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];
    $profilePhoto = $user['profile_photo']; // Default to existing photo

    // Handle profile photo upload
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/profile_photos/';
        $fileName = basename($_FILES['profilePhoto']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            die("Upload directory does not exist: " . $uploadDir);
        }

        if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $targetFilePath)) {
            $profilePhoto = 'assets/images/profile_photos/' . $fileName; // Save relative path
        } else {
            die("Error moving uploaded file to target directory.");
        }
    } elseif (isset($_FILES['profilePhoto']['error'])) {
        switch ($_FILES['profilePhoto']['error']) {
            case UPLOAD_ERR_NO_FILE:
                // No file uploaded, keep the existing profile photo
                break;
            default:
                die("File upload error: " . $_FILES['profilePhoto']['error']);
        }
    } else {
        die("No file was uploaded.");
    }

    try {
        // Update user data in the database
        $stmt = $pdo->prepare("UPDATE user SET first_name = :firstName, last_name = :lastName, email = :email, contact_no = :contactNumber, profile_photo = :profilePhoto WHERE user_id = :user_id");
        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':contactNumber' => $contactNumber,
            ':profilePhoto' => $profilePhoto,
            ':user_id' => $user_id
        ]);

        // Redirect to myprofile.php after successful update
        header("Location: myprofile.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating profile: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../../assets/css/student/editprofile.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome for "+" icon -->
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Edit Profile Content -->
        <main class="dashboard">
            <h1>Edit Profile</h1>

            <form action="" method="post" enctype="multipart/form-data">
            <div class="photo-frame">
                <div class="photo-container">
                    <img src="<?php echo htmlspecialchars($user['profile_photo'] ?: 'https://via.placeholder.com/150'); ?>" alt="Profile Photo" id="profilePhotoPreview" class="profile-photo">
                    <label for="profilePhoto" class="upload-icon">
                        <i class="fas fa-plus"></i> <!-- "+" Icon -->
                    </label>
                    <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewPhoto(event)">
                </div>
            </div>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($user['contact_no']); ?>" required>
                </div>
                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?> 

    <script>
        function previewPhoto(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('profilePhotoPreview');
                output.src = reader.result; // Set the uploaded image as the source
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>

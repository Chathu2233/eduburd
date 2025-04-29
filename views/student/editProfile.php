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
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $profilePhoto = $user['profile_photo']; // Default to existing photo

    $passwordError = "";
    if (!empty($password) || !empty($repassword)) {
        // Check if passwords match
        if ($password !== $repassword) {
            $passwordError = "Passwords do not match.";
        }
        // Check password length
        elseif (strlen($password) < 8) {
            $passwordError = "Password must be at least 8 characters long.";
        }
        // Check for at least one uppercase letter
        elseif (!preg_match('/[A-Z]/', $password)) {
            $passwordError = "Password must contain at least one uppercase letter.";
        }
        // Check for at least one number
        elseif (!preg_match('/[0-9]/', $password)) {
            $passwordError = "Password must contain at least one number.";
        }
        // Check for at least one special character
        elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $passwordError = "Password must contain at least one special character.";
        }

        // If there is an error, show it and exit
        if ($passwordError) {
            echo "<script>alert('$passwordError');</script>";
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }
    // Handle profile photo upload
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/profile_photos/';
        $fileTmpPath = $_FILES['profilePhoto']['tmp_name'];
        $fileName = basename($_FILES['profilePhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Unsupported image file type.");
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            // Save path relative to project root
            $profilePhoto = 'assets/images/profile_photos/' . $newFileName;
        } else {
            die("Failed to move uploaded profile photo.");
        }
    }

    try {
        // Update user data in the database
        $query = "
            UPDATE user 
            SET first_name = :firstName, 
                last_name = :lastName, 
                email = :email, 
                contact_no = :contactNumber, 
                profile_photo = :profilePhoto
        ";
        if (!empty($password)) {
            $query .= ", password = :password";
        }
        $query .= " WHERE user_id = :user_id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':contactNumber', $contactNumber, PDO::PARAM_STR);
        $stmt->bindParam(':profilePhoto', $profilePhoto, PDO::PARAM_STR);
        if (!empty($password)) {
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        }
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = "Profile updated successfully!";

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
    <title>Student Edit Profile</title>
    <link rel="stylesheet" href="../../assets/css/student/editprofile.css">
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

        <!-- Main Content -->
        <main class="main-content">
        
            <h2>Edit Profile</h2>

            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']); // Remove the message after showing it
            }
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="photo-frame">
                    <div class="photo-container">
                        <img 
                            src="<?php echo !empty($user['profile_photo']) ? '../../' . htmlspecialchars($user['profile_photo']) : '../../assets/images/studentpropic.png'; ?>" 
                            alt="Profile Photo" 
                            id="profilePhotoPreview" 
                            class="profile-photo">
                        <label for="profilePhoto" class="add-photo-icon">+</label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewPhoto(event)" style="display: none;">
                    </div>
                </div>
                <div class="profile-box">
                    <label for="firstname"><strong>First Name:</strong></label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="lastname"><strong>Last Name:</strong></label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="email"><strong>E-mail:</strong></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="contactNumber"><strong>Contact Number:</strong></label>
                    <input type="text" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($user['contact_no']); ?>" required>
                </div>
                <div class="profile-box">
                    <label><strong>Password:</strong></label>
                    <a href="#" id="changePasswordLink" onclick="togglePasswordFields(event)">Change Password</a>
                </div>
                <div id="passwordFields" style="display: none;">
                    <div class="profile-box">
                        <label for="password"><strong>New Password:</strong></label>
                        <input type="password" id="password" name="password" placeholder="Enter new password">
                    </div>
                    <div class="profile-box">
                        <label for="repassword"><strong>Re-enter Password:</strong></label>
                        <input type="password" id="repassword" name="repassword" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="button-container">
                    <button type="submit" class="edit-button">Save</button>
                    <button type="button" class="edit-button cancel-button" onclick="window.location.href='myprofile.php'">Cancel</button>
                </div>
            </form>
        </main>
    </div>


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

    function togglePasswordFields(event) {
        event.preventDefault(); // Prevent the default link behavior
        const passwordFields = document.getElementById('passwordFields');
        const changePasswordLink = document.getElementById('changePasswordLink');

        if (passwordFields.style.display === 'none') {
            passwordFields.style.display = 'block'; // Show the password fields
            changePasswordLink.textContent = 'Cancel Change Password'; // Update link text
        } else {
            passwordFields.style.display = 'none'; // Hide the password fields
            changePasswordLink.textContent = 'Change Password'; // Reset link text
        }
    }

    // Function to show success message after form submission
    function showSuccessMessage() {
        const successMessage = document.createElement('div');
        successMessage.classList.add('success-message');
        successMessage.innerHTML = "Profile updated successfully!";
        document.body.appendChild(successMessage); // Append the success message to the body
        
        // Automatically hide the message after 3 seconds
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }

    // Call this function after the profile is successfully updated (after form submission)
    // You can trigger it with PHP, or on form submission if there's no page reload

    // Example: Trigger the success message in JavaScript on form submission
    document.getElementById('editProfileForm').onsubmit = function(event) {
        // Assuming the profile update is successful, trigger the success message
        event.preventDefault(); // Prevent the form from actually submitting for demo purposes
        showSuccessMessage(); // Display success message
        // Here, you would normally allow the form to submit if everything is successful
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        const repassword = document.getElementById('repassword').value;
        const errorMessage = document.getElementById('passwordError');
        let valid = true;
        
        // Clear previous error message
        errorMessage.innerHTML = '';

        // Password length validation
        if (password.length < 8) {
            errorMessage.innerHTML += 'Password must be at least 8 characters long.<br>';
            valid = false;
        }

        // Password match validation
        if (password !== repassword) {
            errorMessage.innerHTML += 'Passwords do not match.<br>';
            valid = false;
        }

        // Password complexity validation (at least 1 uppercase, 1 lowercase, 1 digit)
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
        if (!regex.test(password)) {
            errorMessage.innerHTML += 'Password must contain at least one uppercase letter, one lowercase letter, and one digit.<br>';
            valid = false;
        }

        return valid;
    }


</script>

</body>
</html>
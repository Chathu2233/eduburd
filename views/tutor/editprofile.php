<?php 
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include your database connection file

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch tutor and user information
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.first_name, 
            u.last_name, 
            u.email, 
            u.contact_no, 
            u.profile_photo, 
            t.description, 
            t.years_of_experience, 
            t.cv,
            t.bank_details, 
            t.fee,
            t.link -- Include the link column
        FROM 
            tutor t
        JOIN 
            user u ON t.user_id = u.user_id
        WHERE 
            t.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tutor) {
        die("Tutor not found.");
    }
} catch (PDOException $e) {
    die("Error fetching tutor information: " . $e->getMessage());
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];
    $description = $_POST['description'];
    $yearsOfExperience = $_POST['years_of_experience'];
    $bankDetails = $_POST['bank_details'];
    $tutorFee = $_POST['fee'];
    $link = $_POST['link']; // New field for the link
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $profilePhoto = $tutor['profile_photo'];
    $cv = $tutor['cv'];

// Handle profile photo upload
if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../assets/images/profile_photos/';
    $fileTmpPath = $_FILES['profilePhoto']['tmp_name'];
    $fileName = basename($_FILES['profilePhoto']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Validate file type
    if (!in_array($fileExtension, $allowedExtensions)) {
        die("Unsupported image file type. Allowed types: jpg, jpeg, png, gif, webp.");
    }

    // Validate file size (e.g., max 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES['profilePhoto']['size'] > $maxFileSize) {
        die("File size exceeds the maximum limit of 2MB.");
    }

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique file name
    $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
    $targetFilePath = $uploadDir . $newFileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
        // Save the relative path to the database
        $profilePhoto = 'assets/images/profile_photos/' . $newFileName;
    } else {
        die("Failed to move uploaded profile photo.");
    }
} elseif (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Handle other upload errors
    die("Error uploading profile photo. Please try again.");
}

// Validate passwords
if (!empty($password) || !empty($repassword)) {
    if ($password !== $repassword) {
        die("Passwords do not match.");
    }

    // Validate password strength (e.g., minimum 8 characters, at least one number and one special character)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        die("Password must be at least 8 characters long and include at least one number and one special character.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Validate contact number (e.g., numeric and 10-15 digits)
if (!preg_match('/^\d{10,15}$/', $contactNumber)) {
    die("Invalid contact number. It must be numeric and between 10 to 15 digits.");
}

// Validate tutor fee (e.g., numeric and positive)
if (!is_numeric($tutorFee) || $tutorFee <= 0) {
    die("Tutor fee must be a positive number.");
}

// Validate link (e.g., valid URL format)
if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
    die("Invalid link format. Please enter a valid URL.");
}

// Validate CV upload (if provided)
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $cvUploadDir = '../../assets/documents/cv/';
    $cvTmpPath = $_FILES['cv']['tmp_name'];
    $cvFileName = basename($_FILES['cv']['name']);
    $cvFileExtension = strtolower(pathinfo($cvFileName, PATHINFO_EXTENSION));
    $allowedCvExtensions = ['pdf', 'doc', 'docx'];

    // Validate CV file type
    if (!in_array($cvFileExtension, $allowedCvExtensions)) {
        die("Unsupported CV file type. Allowed types: pdf, doc, docx.");
    }

    // Validate CV file size (e.g., max 5MB)
    $maxCvFileSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES['cv']['size'] > $maxCvFileSize) {
        die("CV file size exceeds the maximum limit of 5MB.");
    }

    // Ensure the CV upload directory exists
    if (!is_dir($cvUploadDir)) {
        mkdir($cvUploadDir, 0777, true);
    }

    // Generate a unique file name for the CV
    $newCvFileName = uniqid('cv_', true) . '.' . $cvFileExtension;
    $cvTargetFilePath = $cvUploadDir . $newCvFileName;

    // Move the uploaded CV to the target directory
    if (move_uploaded_file($cvTmpPath, $cvTargetFilePath)) {
        // Save the relative path to the database
        $cv = 'assets/documents/cv/' . $newCvFileName;
    } else {
        die("Failed to move uploaded CV.");
    }
} elseif (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Handle other upload errors
    die("Error uploading CV. Please try again.");
}
    try {
        // Update user table
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
        $stmt->bindParam(':user_id', $tutor['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Update tutor table
        $stmt = $pdo->prepare("
            UPDATE tutor 
            SET description = :description, 
                years_of_experience = :yearsOfExperience, 
                bank_details = :bankDetails, 
                fee = :tutorFee, 
                link = :link, -- Update the link column
                cv = :cv
            WHERE tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':description' => $description,
            ':yearsOfExperience' => $yearsOfExperience,
            ':bankDetails' => $bankDetails,
            ':tutorFee' => $tutorFee,
            ':link' => $link, // Bind the link value
            ':cv' => $cv,
            ':tutor_id' => $tutor_id
        ]);

        // Redirect to avoid form resubmission
        header("Location: my_account.php");
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
    <link rel="stylesheet" href="../../assets/css/tutor/editprofile.css"> <!-- Sidebar styles -->
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <div class="container">
<?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

        <!-- Edit Profile Content -->
        <main class="content-section">
            <h2>Edit Profile</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="photo-frame">
                    <div class="photo-container">
                        <img 
                            src="<?php echo !empty($tutor['profile_photo']) ? '../../' . htmlspecialchars($tutor['profile_photo']) : '../../assets/images/studentpropic.png'; ?>" 
                            alt="Profile Photo" 
                            id="profilePhotoPreview" 
                            class="profile-photo">
                        <label for="profilePhoto" class="add-photo-icon">+</label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewPhoto(event)" style="display: none;">
                    </div>
                </div>
                <div class="profile-box">
                    <label for="firstname"><strong>First Name:</strong></label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($tutor['first_name']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="lastname"><strong>Last Name:</strong></label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($tutor['last_name']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="email"><strong>E-mail:</strong></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($tutor['email']); ?>" required>
                </div>
                <div class="profile-box">
                        <label for="contactNumber"><strong>Contact Number:</strong></label>
                        <input 
                            type="text" 
                            id="contactNumber" 
                            name="contactNumber" 
                            value="<?php echo htmlspecialchars($tutor['contact_no']); ?>" 
                            required 
                            pattern="\d{10}" 
                            title="Contact number must be exactly 10 digits."
                        >
                    </div>

                    <script>
                        document.getElementById('contactNumber').addEventListener('input', function () {
                        const contactNumberField = this;
                        const contactNumber = contactNumberField.value;

                    // Check if the contact number is exactly 10 digits
                    if (!/^\d{10}$/.test(contactNumber)) {
                        contactNumberField.setCustomValidity("Contact number must be exactly 10 digits.");
                    } else {
                        contactNumberField.setCustomValidity(""); // Clear the error
                    }
                    });
                </script>
                <div class="profile-box">
                    <label for="description"><strong>Description:</strong></label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($tutor['description']); ?></textarea>
                </div>
                <div class="profile-box">
                    <label for="years_of_experience"><strong>Years of Experience:</strong></label>
                    <input type="number" id="years_of_experience" name="years_of_experience" value="<?php echo htmlspecialchars($tutor['years_of_experience']); ?>" required>
                </div>
                <div class="profile-box">
                    <label for="cv"><strong>Upload CV:</strong></label>
                    <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
                </div>
                <div class="profile-box">
                    <label for="bank_details"><strong>Bank Details:</strong></label>
                    <textarea id="bank details" name="bank details" required><?php echo htmlspecialchars($tutor['bank_details']); ?></textarea>
                </div>
                <div class="profile-box">
                    <label for="fee"><strong>Tutor Fee:</strong></label>
                    <input type="text" id="fee" name="fee" value="<?php echo htmlspecialchars($tutor['fee']); ?>" required placeholder="e.g., Rs1000">
                </div>
                <div class="profile-box">
                    <label for="link"><strong>Link:</strong></label>
                    <input type="url" id="link" name="link" value="<?php echo htmlspecialchars($tutor['link']); ?>" placeholder="Enter your link (e.g., LinkedIn, portfolio)" required>
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
                    <button type="button" class="edit-button cancel-button" onclick="window.location.href='my_account.php'">Cancel</button>
                </div>
            </form>
        </main>
    </div>
        </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>

    <script>
        function previewPhoto(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('profilePhotoPreview');
                output.src = reader.result; // Set the uploaded image as the source
            };
            reader.readAsDataURL(event.target.files[0]);
        }

           // Validate passwords
           if (password !== repassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    
    </script>
</body>
</html>
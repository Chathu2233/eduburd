<!-- filepath: c:\xampp\htdocs\eduburd\views\student\editProfile.php -->
<?php 
session_start();
require_once '../db.php'; // Include your database connection file

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch tutor and user information, including profile photo, years of experience, CV, and fee
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.contact_no, 
            u.profile_photo, 
            t.description, 
            t.years_of_experience, 
            t.cv, 
            t.fee
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
    $fee = $_POST['fee'];
    $profilePhoto = $tutor['profile_photo']; // Default to existing profile photo
    $cv = $tutor['cv']; // Default to existing CV

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
    }

    // Handle CV upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $cv_tmp_path = $_FILES['cv']['tmp_name'];
        $cv_name = basename($_FILES['cv']['name']);
        $cv_upload_path = __DIR__ . '/uploads/' . $cv_name;

        if (!move_uploaded_file($cv_tmp_path, $cv_upload_path)) {
            die("Failed to upload CV.");
        } else {
            $cv = 'uploads/' . $cv_name; // Save relative path
        }
    }

    try {
        // Update user table
        $stmt = $pdo->prepare("
            UPDATE user 
            SET first_name = :firstName, 
                last_name = :lastName, 
                email = :email, 
                contact_no = :contactNumber, 
                profile_photo = :profilePhoto 
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':contactNumber' => $contactNumber,
            ':profilePhoto' => $profilePhoto,
            ':user_id' => $tutor['user_id']
        ]);

        // Update tutor table
        $stmt = $pdo->prepare("
            UPDATE tutor 
            SET description = :description, 
                years_of_experience = :yearsOfExperience, 
                cv = :cv, 
                fee = :fee
            WHERE tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':description' => $description,
            ':yearsOfExperience' => $yearsOfExperience,
            ':cv' => $cv,
            ':fee' => $fee,
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
    <link rel="stylesheet" href="../../assets/css/tutor/editprofile.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css"> <!-- Sidebar styles -->
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_tutor.php'; ?>
    </header>

    <div class="container">
<?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

        <!-- Edit Profile Content -->
        <main class="dashboard">
            <h1>Edit Profile</h1>   

            <form action="" method="post" enctype="multipart/form-data">
                <div class="photo-frame">
                    <div class="photo-container">
                        <img src="../../<?= htmlspecialchars($tutor['profile_photo'] ?: 'assets/images/default_profile.png') ?>" alt="Profile Photo" id="profilePhotoPreview" class="profile-photo">
                        <label for="profilePhoto" class="upload-icon">
                            <i class="fas fa-plus"></i> <!-- "+" Icon -->
                        </label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewPhoto(event)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($tutor['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($tutor['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($tutor['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($tutor['contact_no']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($tutor['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="years_of_experience">Years of Experience</label>
                    <input type="number" id="years_of_experience" name="years_of_experience" value="<?php echo htmlspecialchars($tutor['years_of_experience']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fee">Fee (Monthly in USD)</label>
                    <input type="number" id="fee" name="fee" value="<?php echo htmlspecialchars($tutor['fee']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="cv">Upload CV</label>
                    <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
                    <?php if (!empty($tutor['cv'])): ?>
                        <p>Current CV: <a href="<?= htmlspecialchars($tutor['cv']) ?>" target="_blank">View CV</a></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </main>
    </div>
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
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
    // After retrieving $user
try {
    $stmt2 = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $stmt2->execute([':user_id' => $user_id]);
    $student = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $student_id = $student['student_id'];
    } else {
        $student_id = null; // or handle error if needed
    }
} catch (PDOException $e) {
    die("Database error (student): " . $e->getMessage());
}


    // Resolve the profile photo path
    $profilePhotoPath = '../../' . $user['profile_photo'];
    if (!file_exists($profilePhotoPath) || empty($user['profile_photo'])) {
        $profilePhotoPath = '../../assets/images/studentpropic.png'; // Fallback to default photo
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../../assets/css/student/myprofile.css">
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
            <section class="welcome">
             <div class="profile-container1">
                

                    <h2>My Profile</h2>
                    <img src="<?= htmlspecialchars($profilePhotoPath) ?>" alt="Profile Picture">
                    <div class="profile-details">
                    <div class="profile-box">
                            <p><strong>Student ID: </strong><?= htmlspecialchars($student_id); ?></p>
                        </div>
                        <div class="profile-box">
                            <p><strong>First Name: </strong><?= htmlspecialchars($user['first_name']); ?></p>
                        </div>
                        <div class="profile-box">
                            <p><strong>Last Name: </strong><?= htmlspecialchars($user['last_name']); ?></p>
                        </div>
                        <div class="profile-box">
                            <p><strong>Email: </strong><?= htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div class="profile-box">
                            <p><strong>Contact Number: </strong><?= htmlspecialchars($user['contact_no']); ?></p>
                        </div>
                    </div>
                    <div>
                        <button class="edit-button"><a href="editprofile.php">Edit</a></button>
                    </div>
                </div>
            </section>
        </main>
    </div>
   >

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
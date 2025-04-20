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

    // Resolve the profile photo path
    $profilePhotoPath = '../../' . $user['profile_photo'];
    if (!file_exists($profilePhotoPath) || empty($user['profile_photo'])) {
        $profilePhotoPath = 'https://via.placeholder.com/150'; // Fallback to placeholder
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
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
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

        <!-- Profile Content -->
        <main class="dashboard">
            <div class="profile-card">
                <h1>My Profile</h1>
                <div class="photo-frame">
                    <div class="photo-container">
                        <img src="<?php echo htmlspecialchars($profilePhotoPath); ?>" alt="Profile Photo" class="profile-photo">
                    </div>
                </div>
                <h1 class="student-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p class="student-info">ID: <?php echo htmlspecialchars($user['user_id']); ?></p>
                <p class="student-info">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="student-info">Contact Number: <?php echo htmlspecialchars($user['contact_no']); ?></p>
                <button class="edit-button" onclick="window.location.href='editprofile.php'">Edit Profile</button>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

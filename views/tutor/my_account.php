<?php
session_start();
require_once '../db.php'; // Include your database connection file

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch tutor and user information, including profile photo and fee
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.contact_no, 
            u.profile_photo, 
            t.description, 
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/my_profile.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css"> <!-- Sidebar styles -->
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="container">
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Centered images" width="50" height="50" style="margin-top: 30px;">
        <ul>
            <div class="sidebar1">
                <li><a href="my_account.php"><i class="fas fa-user"></i> My Profile</a></li>
            </div>
            <div class="sidebar2">
                <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i> My Subjects</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="student_request.php"><i class="fas fa-user-plus"></i> Student Requests</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="time_request.php"><i class="fas fa-user-plus"></i> Time slot Requests</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="announcement.php">Announcements</a></li>
            </div>
            <div class="sidebar5">
                <li><a href="../resourcelibrary.php">Resource Library</a></li>
            </div>
            <div class="sidebar6">
                <li><a href="editprofile.php">Edit Profile</a></li>
            </div>
        </ul>
    </div>

    <main class="dashboard">
        <div class="profile-container">
            <div class="profile-section">
                <h2>My Profile</h2>
                <div class="profile-image">
                    <img src="../../<?= htmlspecialchars($tutor['profile_photo']) ?>" alt="Profile Image">
                </div>
                <div class="profile-details">
                    <p><strong>First Name:</strong> <?= htmlspecialchars($tutor['first_name']) ?></p>
                    <p><strong>Last Name:</strong> <?= htmlspecialchars($tutor['last_name']) ?></p>
                    <p><strong>E-Mail:</strong> <?= htmlspecialchars($tutor['email']) ?></p>
                    <p><strong>Contact Number:</strong> <?= htmlspecialchars($tutor['contact_no']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($tutor['description']) ?></p>
                    <p><strong>Fee (Monthly):</strong> <?= htmlspecialchars($tutor['fee']) ?> USD</p>
                </div>
                <a href="editprofile.php" class="edit-button">Edit Profile</a>
            </div>
        </div>
    </main>
</div>

<!-- Footer Section -->
<?php include '../footer.php'; ?>
</body>
</html>

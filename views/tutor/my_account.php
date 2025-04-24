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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/my_profile.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="container">
<?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->
        <div class="content-section">
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
            <button class="back-btn" onclick="history.back()">
    <i class="fas fa-arrow-left"></i> 
</button>
        </div>
        
    
</div>
</div>

<!-- Footer Section -->
<?php include '../footer.php'; ?>
</body>
</html>

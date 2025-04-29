<?php
session_start();
require_once '../db.php'; // Include your database connection file

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch tutor and user information, including profile photo, fee, and link
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
            t.bank_details,
            t.fee,
            t.link
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
    <link rel="stylesheet" href="../../assets/css/Tutor/my_profile.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="container">
    <?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

    <!-- Main Layout -->
    <div class="main-layout">
        <main class="content-section">
            <div class="profile-container1">
                <h2>My Profile</h2>
                <img src="../../<?= htmlspecialchars($tutor['profile_photo'] ?: 'assets/images/studentpropic.png') ?>" alt="Profile Image">
                <div class="profile-details">
                    <div class="profile-box"><p><strong>First Name:</strong> <?= htmlspecialchars($tutor['first_name']) ?></p></div>
                    <div class="profile-box"><p><strong>Last Name:</strong> <?= htmlspecialchars($tutor['last_name']) ?></p></div>
                    <div class="profile-box"><p><strong>E-Mail:</strong> <?= htmlspecialchars($tutor['email']) ?></p></div>
                    <div class="profile-box"><p><strong>Contact Number:</strong> <?= htmlspecialchars($tutor['contact_no']) ?></p></div>
                    <div class="profile-box"><p><strong>Description:</strong> <?= htmlspecialchars($tutor['description']) ?></p></div>
                    <div class="profile-box"><p><strong>Bank Details:</strong> <?= htmlspecialchars($tutor['bank_details']) ?></p></div>
                    <div class="profile-box"><p><strong>Tutor Fees:</strong> <?= htmlspecialchars($tutor['fee']) ?></p></div>
                    <div class="profile-box"><p><strong>Link:</strong> <a href="<?= htmlspecialchars($tutor['link']) ?>" target="_blank"><?= htmlspecialchars($tutor['link']) ?></a></p></div>
                </div>
                <div>
                    <button class="edit-button"><a href="editprofile.php">Edit Profile</a></button>
                </div>
            </div>
        </main>
    </div>
</div>
</div>

<!-- Footer Section -->
<?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>

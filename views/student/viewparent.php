<?php
session_start();
require '../db.php'; // Include database connection

// Ensure the student is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

// Get parent_id from the query string
if (!isset($_GET['parent_id']) || !is_numeric($_GET['parent_id'])) {
    die("Invalid or missing parent ID.");
}

$parent_id = intval($_GET['parent_id']);

try {
    // Step 1: Get user_id from parent table using parent_id
    $parentQuery = $pdo->prepare("SELECT user_id FROM parent WHERE parent_id = :parent_id");
    $parentQuery->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $parentQuery->execute();

    if ($parentQuery->rowCount() === 0) {
        die("Parent details not found.");
    }

    $parentData = $parentQuery->fetch(PDO::FETCH_ASSOC);
    $user_id = $parentData['user_id'];

    // Step 2: Fetch parent details from user table
    $userQuery = $pdo->prepare("SELECT first_name, last_name, email, contact_no, profile_photo FROM user WHERE user_id = :user_id");
    $userQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $userQuery->execute();

    if ($userQuery->rowCount() === 0) {
        die("User details not found.");
    }

    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    // Resolve the profile photo path
    $profilePhotoPath = '../../' . $user['profile_photo'];
    if (!file_exists($profilePhotoPath) || empty($user['profile_photo'])) {
        $profilePhotoPath = '../../assets/images/default_profile.png'; // Fallback to default photo
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
    <title>Parent Profile</title>
    <link rel="stylesheet" href="../../assets/css/student/viewparent.css">
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

        <!-- Parent Content -->
        <main class="dashboard">
            <section>
           

                <div class="profile-container1">
                    <h2>Parent Profile</h2>
                    <img src="<?= htmlspecialchars($profilePhotoPath) ?>" alt="Profile Picture">
                    <div class="profile-details">
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
                </div>
              
            </section>
            <div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>
        </main>
    </div>
   

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
<?php require_once 'constants.php'; ?>

<?php
// Start session only if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default values
$user_name = "Guest";
$profile_photo = ""; // No profile picture by default

// Check if the user is logged in and is a tutor
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'tutor') {
    // Retrieve the first name and profile photo directly from the session
    $user_name = $_SESSION['first_name'] ?? "Tutor";
    if (!empty($_SESSION['profile_photo'])) {
        $profile_photo = ROOT . "/uploads/" . $_SESSION['profile_photo']; // Use the profile photo from the session
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/header_student.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="<?php echo ROOT; ?>/assets/images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo">
        </div>
        <nav>
            <ul>
                <li><a href="<?php echo ROOT; ?>/views/home.php">Home</a></li>
                <li><a href="<?php echo ROOT; ?>/views/subjectweoffer.php">Subjects</a></li>
                <li><a href="<?php echo ROOT; ?>/views/tutor/subject.php">My Subjects</a></li>
                <li><a href="<?php echo ROOT; ?>/views/tutor/update_time.php">Update Schedules</a></li>
                <li><a href="<?php echo ROOT; ?>/views/tutor/my_account.php">My Profile</a></li>
                <li><a href="<?php echo ROOT; ?>/views/tutor/tutor_dashboard.php">My Dashboard</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tutor'): ?>
                <!-- Profile Picture or Icon and Dropdown for logged-in tutor -->
                <div class="profile-container">
                    <?php if (!empty($profile_photo)): ?>
                        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="profile-pic">
                    <?php else: ?>
                        <i class="default-profile-icon"></i> <!-- Default icon if no profile picture -->
                    <?php endif; ?>
                    <div class="dropdown">
                        <button class="dropdown-btn">Hi, <?php echo htmlspecialchars($user_name); ?></button>
                        <div class="dropdown-content">
                            <a href="<?php echo ROOT; ?>/views/tutor/my_account.php">My Profile</a>
                            <a href="<?php echo ROOT; ?>/views/logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Default Logout Button -->
                <a href="<?php echo ROOT; ?>/views/logout.php" class="login-btn">Logout</a>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>
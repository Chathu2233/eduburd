<?php

require_once 'constants.php';

// Start session only if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default values
$user_name = "Guest";
$profile_photo = ""; // No profile picture by default

// Check if the user is logged in and is a parent
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'parent') {
    // Retrieve the first name and profile photo directly from the session
    $user_name = $_SESSION['first_name'] ?? "Parent";
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
    <title>Parent Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/header_student.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="<?php echo ROOT; ?>/assets/images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo">
    </div>
    <nav>
        <ul>
            <li><a href="<?php echo ROOT; ?>/views/home.php">Home</a></li>
            <li><a href="<?php echo ROOT; ?>/views/findatutor.php">Find A Tutor</a></li>
            <li><a href="<?php echo ROOT; ?>/views/tutorsignup.php">Become A Tutor</a></li>
            <li><a href="<?php echo ROOT; ?>/views/aboutus.php">About Us</a></li>
            <li><a href="<?php echo ROOT; ?>/views/parent/dashboard.php">My Dashboard</a></li>
        </ul>
    </nav>
    <div class="auth-buttons">
        <!-- Notifications Bell Icon -->
        <div class="notifications-container">
            <i class="bell-icon" onclick="toggleNotifications()">
                <span id="notification-count" class="notification-count"></span>
            </i>
            <div id="notifications-popup" class="notifications-popup">
                <h3>Notifications</h3>
                <ul id="notifications-list">
                    <!-- Notifications will be dynamically added here -->
                </ul>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'parent'): ?>
            <!-- Profile Picture or Icon and Dropdown for logged-in parent -->
            <div class="profile-container">
                <?php if (!empty($profile_photo)): ?>
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="profile-pic">
                <?php else: ?>
                    <!-- Default profile picture -->
                    <img src="<?php echo ROOT; ?>/assets/images/studentpropic.png" alt="Default Profile Picture" class="profile-pic">
                <?php endif; ?>
                <div class="dropdown">
                    <button class="dropdown-btn">Hi, <?php echo htmlspecialchars($user_name); ?></button>
                    <div class="dropdown-content">
                        <a href="<?php echo ROOT; ?>/views/parent/parentprofilepage.php">My Profile</a>
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

<script>
    let notificationsFetched = false;

    function toggleNotifications() {
        const popup = document.getElementById('notifications-popup');
        const notificationCount = document.getElementById('notification-count');

        // Toggle the visibility of the popup
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';

        // Clear the notification count when the popup is opened
        if (popup.style.display === 'block') {
            notificationCount.textContent = ''; // Clear the count
            notificationCount.style.display = 'none'; // Hide the badge
        }

        // Fetch notifications only once when the bell icon is clicked
        if (!notificationsFetched) {
            fetchNotifications();
            notificationsFetched = true;
        }
    }

    function fetchNotifications() {
        fetch('<?php echo ROOT; ?>/views/parent/parent_fetchnotifications.php')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notifications-list');
                const notificationCount = document.getElementById('notification-count');
                notificationsList.innerHTML = ''; // Clear existing notifications

                if (data.length === 0) {
                    const noNotifications = document.createElement('li');
                    noNotifications.textContent = 'No new notifications.';
                    notificationsList.appendChild(noNotifications);
                    notificationCount.textContent = ''; // Clear the count
                    notificationCount.style.display = 'none'; // Hide the badge
                } else {
                    // Add notifications in reverse order (latest first)
                    data.reverse().forEach(notification => {
                        const notificationItem = document.createElement('li');
                        notificationItem.textContent = notification.text;
                        notificationsList.appendChild(notificationItem);
                    });
                    notificationCount.textContent = data.length; // Update the count
                    notificationCount.style.display = 'block'; // Show the badge
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
</script>
</body>
</html>
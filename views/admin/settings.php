<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="/assets/css/admin/settings.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
</head>
<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>

<body>


<section class="settings-page">
    <div class="settings-container">
        <h2>Settings</h2>
        <form class="settings-form">
            <!-- Profile Settings -->
            <div class="settings-group">
                <h3>Profile Settings</h3>
                <label for="username">Username</label>
                <input type="text" id="username" placeholder="Enter your username">
                
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Enter your email">
            </div>

            <!-- Security Settings -->
            <div class="settings-group">
                <h3>Security</h3>
                <label for="current-password">Current Password</label>
                <input type="password" id="current-password" placeholder="Enter your current password">
                
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" placeholder="Enter a new password">
            </div>

            <!-- Preferences -->
            <div class="settings-group">
                <h3>Preferences</h3>
                <label for="theme">Theme</label>
                <select id="theme">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                </select>

                <label for="notifications">Notifications</label>
                <input type="checkbox" id="notifications" checked>
                <span>Enable Email Notifications</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</section>
<?php include '../footer.php'; ?>
</body>
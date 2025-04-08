<?php
session_start();
include '../db.php';
require_once '../constants.php';

// Fetch current user settings
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, notifications FROM user WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $theme = $_POST['theme'];
    $notifications = isset($_POST['notifications']) ? 1 : 0;

    // Update user settings
    $update_query = "UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email, notifications = :notifications WHERE user_id = :user_id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':notifications' => $notifications,
        ':user_id' => $user_id
    ]);

    // Update password if provided
    if (!empty($new_password)) {
        $password_query = "SELECT password FROM user WHERE user_id = :user_id";
        $password_stmt = $pdo->prepare($password_query);
        $password_stmt->execute([':user_id' => $user_id]);
        $current_hashed_password = $password_stmt->fetch(PDO::FETCH_ASSOC)['password'];

        if (password_verify($current_password, $current_hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE user SET password = :password WHERE user_id = :user_id";
            $update_password_stmt = $pdo->prepare($update_password_query);
            $update_password_stmt->execute([
                ':password' => $new_hashed_password,
                ':user_id' => $user_id
            ]);
        } else {
            $error_message = "Current password is incorrect.";
        }
    }

    // Update theme in session
    $_SESSION['theme'] = $theme;

    header('Location: ' . ROOT . '/views/admin/settings.php');
    exit();
}

// Set theme from session or default to light
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/settings.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link id="theme-stylesheet" rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/<?php echo $theme; ?>.css">
</head>
<header>
    <?php include '../header_admin.php'; ?>
</header>
<body>

<section class="settings-page">
    <div class="settings-container">
        <h2>Settings</h2>
        <form class="settings-form" method="POST">
            <!-- Profile Settings -->
            <div class="settings-group">
                <h3>Profile Settings</h3>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" placeholder="Enter your first name" required>
                
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" placeholder="Enter your last name" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter your email" required>
            </div>

            <!-- Security Settings -->
            <div class="settings-group">
                <h3>Security</h3>
                <label for="current-password">Current Password</label>
                <input type="password" id="current-password" name="current_password" placeholder="Enter your current password">
                
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" placeholder="Enter a new password">
            </div>

            <!-- Preferences -->
            <div class="settings-group">
                <h3>Preferences</h3>
                <label for="theme">Theme</label>
                <select id="theme" name="theme" onchange="changeTheme(this.value)">
                    <option value="light" <?php echo $theme === 'light' ? 'selected' : ''; ?>>Light</option>
                    <option value="dark" <?php echo $theme === 'dark' ? 'selected' : ''; ?>>Dark</option>
                </select>

                <label for="notifications">Email Notifications</label>
                <input type="checkbox" id="notifications" name="notifications" <?php echo $user['notifications'] ? 'checked' : ''; ?>>
                <span>Enable Email Notifications</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</section>
<?php include '../footer.php'; ?>

<script>
function changeTheme(theme) {
    document.getElementById('theme-stylesheet').setAttribute('href', '<?php echo ROOT; ?>/assets/css/admin/' + theme + '.css');
}
</script>
</body>
</html>
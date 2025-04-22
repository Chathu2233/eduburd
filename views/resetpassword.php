<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $re_password = $_POST['re_password'];

    // Check if passwords match
    if ($password !== $re_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'resetpassword.php?token=$token';</script>";
        exit;
    }

    // Validate the token
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password
        $stmt = $pdo->prepare("UPDATE user SET password = :password WHERE email = :email");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $reset['email']);
        $stmt->execute();

        // Delete the token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        echo "<script>alert('Password reset successful!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href = 'login.php';</script>";
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
} else {
    die('Invalid request.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/header_guest.css">
</head>
<body>
    <header>
        <?php include 'header_guest.php'; ?>
    </header>
    <div class="wrapper">
        <form method="POST" action="resetpassword.php">
            <h1>Reset Password</h1>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="New Password" required>
                <i class='bx bxs-lock-alt' id="togglePassword1" style="cursor: pointer;"></i>
            </div>
            <div class="input-box">
                <input type="password" id="re_password" name="re_password" placeholder="Re-enter New Password" required>
                <i class='bx bxs-lock-alt' id="togglePassword2" style="cursor: pointer;"></i>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <div class="register-link">
            <p>Remembered your password? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        // Toggle visibility for the first password field
        const togglePassword1 = document.getElementById('togglePassword1');
        const passwordInput1 = document.getElementById('password');

        togglePassword1.addEventListener('click', function () {
            const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput1.setAttribute('type', type);
            this.classList.toggle('bxs-lock-alt');
            this.classList.toggle('bxs-lock-open-alt');
        });

        // Toggle visibility for the second password field
        const togglePassword2 = document.getElementById('togglePassword2');
        const passwordInput2 = document.getElementById('re_password');

        togglePassword2.addEventListener('click', function () {
            const type = passwordInput2.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput2.setAttribute('type', type);
            this.classList.toggle('bxs-lock-alt');
            this.classList.toggle('bxs-lock-open-alt');
        });
    </script>
</body>
</html>
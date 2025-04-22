<?php
require 'db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(16));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours')); // Extend to 24 hours for testing

        // Insert the token into the password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();

        // Send the reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fsajida742@gmail.com'; 
            $mail->Password   = 'kxhu yvdb nlvi pkix';      
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('fsajida742@gmail.com', 'Eduburd');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Hi,<br><br>Click the link below to reset your password:<br>
                              <a href='http://localhost/eduburd/views/resetpassword.php?token=$token'>Reset Password</a><br><br>
                              This link will expire in 1 hour.";

            $mail->send();
            echo "<script>alert('Password reset email sent! Please check your inbox.'); window.location.href = 'login.php';</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('Email not found.'); window.location.href = 'forgotpassword.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../assets/css/login.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../assets/css/header_guest.css">
</head>
<body>
  <header>
    <?php
    include 'header_guest.php'; // For guests (not logged in)
    ?>
  </header>
  <div class="wrapper">
    <form action="forgotpassword.php" method="POST">
      <h1>Forgot password</h1>
      <div class="input-box">
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        <i class='bx bxs-envelope'></i>
      </div>
      <button type="submit" class="btn">Send Reset Link</button>
    </form>
    <div class="register-link">
      <p>Remembered your password? <a href="login.php">Login</a></p>
    </div>
  </div>
</body>
</html>
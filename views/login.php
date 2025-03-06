<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the database for the provided email
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Compare passwords (use password_verify if passwords are hashed)
        if ($password === $user['password']) {
            // Store user information in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['user_role'];

            // Respond with success
            echo json_encode(['status' => 'success', 'message' => 'Login successful']);
        } else {
            // Respond with error for incorrect password
            echo json_encode(['status' => 'error', 'message' => 'Incorrect password']);
        }
    } else {
        // Respond with error for invalid email
        echo json_encode(['status' => 'error', 'message' => 'Email not found']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="../assets/css/login.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../assets/css/header_guest.css">
</head>
<body>
  <header>
    <?php

  
    {
        include 'header_guest.php'; // For guests (not logged in)
    }
?>
    
  </header>
  <div class="wrapper">
    <form action="login.php" method="POST">
      <h1>Login</h1>
      <div class="input-box">
        <input type="text" name="email" placeholder="Username" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
  <input type="password" id="password" name="password" placeholder="Password" required>
  <i class='bx bxs-lock-alt' id="togglePassword" style="cursor: pointer;"></i>
  </div>
  <div class="remember-forgot">
        <label><input type="checkbox">Remember Me</label>
        <a href="#">Forgot Password</a>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
    <div class="register-link">
      <p>Don't have an account? <a href="signupmenu.php">Register</a></p>
    </div>
  </div>

  
<script>
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    // Toggle the password visibility
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Change the lock icon to indicate the state
    this.classList.toggle('bxs-lock-alt'); // Closed lock icon
    this.classList.toggle('bxs-lock-open-alt'); // Open lock icon
  });
   
    document.querySelector('form').addEventListener('submit', function(event) {
      event.preventDefault();  // Prevent traditional form submission
      fetch('login.php', {
          method: 'POST',
          body: new URLSearchParams(new FormData(this))
      })
      .then(response => response.json())
      .then(data => {
          console.log(data);  // Log the response to see what the server is sending
          if (data.status === 'success') {
              alert(data.message);
              window.location.href = 'home.php';
          } else {
              alert(data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
      });
    });
  </script>
</body>
</html>
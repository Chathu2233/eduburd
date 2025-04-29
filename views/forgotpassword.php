
        echo "<script>alert('Email not found.'); window.location.href = 'forgotpassword.php';</script>";
    }
}
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
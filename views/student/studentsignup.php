<?php
session_start();

require '../db.php';
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data for the user
    $user_role = $_POST['user_role'];
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $contact_no = $_POST['contactNumber'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $re_password = $_POST['reEnterPassword'];

    // Check if passwords match
    if ($password !== $re_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    // Check if email is already registered
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit;
    }

    // Begin transaction to insert into both tables
    $pdo->beginTransaction();

    try {
        // Generate a unique verification code
        $verification_code = bin2hex(random_bytes(16));

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into user table with verification code
        $stmt = $pdo->prepare("INSERT INTO user (user_role, first_name, last_name, email, contact_no, dob, password, verification_code) 
                               VALUES (:user_role, :first_name, :last_name, :email, :contact_no, :dob, :password, :verification_code)");
        $stmt->bindParam(':user_role', $user_role);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact_no', $contact_no);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':verification_code', $verification_code);
        $stmt->execute();

        // Get the user_id of the inserted user
        $user_id = $pdo->lastInsertId();

        // Insert into student table with the user_id as foreign key
        $stmt = $pdo->prepare("INSERT INTO student (user_id) VALUES (:user_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Send verification email
        $mail = new PHPMailer(true);
ase check your email to verify your account.');
                window.location.href = '../login.php';
              </script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up</title>
    <link rel="stylesheet" href="../../assets/css/signup.css">

    <!-- Font and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_guest.php'; ?>
    </header>

    <!-- Sign Up Form Section -->
    <main>
        <div class="signup-container">
            <div class="signup-form">
            <div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>

                <h3>Student Signup</h3>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form id="signupForm" action="studentsignup.php" method="post">
    <!-- Hidden field to store the role -->
    <input type="hidden" name="user_role" value="student">
    
    <label for="first-name">First Name:</label>
    <input type="text" id="first-name" name="firstName" required>
    
    <label for="last-name">Last Name:</label>
    <input type="text" id="last-name" name="lastName" required>
    
    <label for="contact-number">Contact Number:</label>
    <input type="text" id="contact_number" name="contact_number" pattern="[0-9]{10}" required>

    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" max="<?php echo date('Y-m-d'); ?>" required>
    <script>
        // Set the maximum date for the DOB field to today's date
        const dobInput = document.getElementById('dob');
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        dobInput.setAttribute('max', today); // Set the max attribute to today's date
    </script>
    
    <label for="password">Password:</label>
<div class="password-container">
    <input type="password" id="password" name="password" required>
    <i class='bx bxs-lock-alt' id="togglePassword" style="cursor: pointer;"></i>
</div>

<label for="reEnterPassword">Re-enter Password:</label>
<div class="password-container">
    <input type="password" id="reEnterPassword" name="reEnterPassword" required>
    <i class='bx bxs-lock-alt' id="toggleReEnterPassword" style="cursor: pointer;"></i>
</div>

<script>
    // Toggle visibility for the password field
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bxs-lock-alt');
        this.classList.toggle('bxs-lock-open-alt');
    });

    // Toggle visibility for the re-enter password field
    const toggleReEnterPassword = document.getElementById('toggleReEnterPassword');
    const reEnterPasswordInput = document.getElementById('reEnterPassword');

    toggleReEnterPassword.addEventListener('click', function () {
        const type = reEnterPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        reEnterPasswordInput.setAttribute('type', type);
        this.classList.toggle('bxs-lock-alt');
        this.classList.toggle('bxs-lock-open-alt');
    });
</script>
    <div class="form-buttons">
        <button type="submit" class="submit-btn">Sign Up</button>
        <button type="reset" class="cancel-btn">Reset</button>
    </div>
</form>
                
            </div>
            <div class="already-account">
                <p>Already have an account?</p>
                <a href="../login.php" class="login-large-btn">Login</a>
            </div>
        </div>
    </main>

    
    <!-- Footer Section -->
    <?php include '../footer.php'; ?>
</body>
</html>
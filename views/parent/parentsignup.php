<?php
session_start();

require '../db.php';
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data for the user
    $user_role = $_POST['user_role'];
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $contact_no = $_POST['contactNumber'];
    $password = $_POST['password'];
    $re_password = $_POST['reEnterPassword'];

    // Parent-specific data
    $nic = $_POST['nic'];

    // Check if passwords match
    if ($password !== $re_password) {
        $error_message = 'Passwords do not match';
    } else {
        // Check if email is already registered
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = 'Email already exists';
        } else {
            // Begin transaction to insert into both tables
            $pdo->beginTransaction();

            try {
                // Generate a unique verification code
                $verification_code = bin2hex(random_bytes(16));

                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert into user table with verification code
                $stmt = $pdo->prepare("INSERT INTO user (user_role, first_name, last_name, email, contact_no, password, verification_code) 
                                       VALUES (:user_role, :first_name, :last_name, :email, :contact_no, :password, :verification_code)");
                $stmt->bindParam(':user_role', $user_role);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':contact_no', $contact_no);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':verification_code', $verification_code);
                $stmt->execute();

                // Get the user_id of the inserted user
                $user_id = $pdo->lastInsertId();

                // Insert into parent table with the user_id as foreign key
                $stmt = $pdo->prepare("INSERT INTO parent (user_id, nic) VALUES (:user_id, :nic)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':nic', $nic);
                $stmt->execute();

                // Commit the transaction
                $pdo->commit();

                // Send vEmail';
                    $mail->Body    = "Hi $first_name,<br><br>Please verify your email by clicking the link below:<br>
                                      <a href='http://localhost/eduburd/views/verify.php?code=$verification_code'>Verify Email</a><br><br>Thank you!";

                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                // Automatically log the user in by setting session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_role'] = $user_role;
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;

                echo "<script>
                        alert('Registration successful! Please check your email to verify your account.');
                        window.location.href = '../login.php';
                      </script>";
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_message = 'An error occurred: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Sign Up</title>
    <link rel="stylesheet" href="../../assets/css/signup.css?v=<?php echo time(); ?>">

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
                <h3>Parent Signup</h3>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form id="signupForm" action="parentsignup.php" method="post">
                    <!-- Hidden field to store the role -->
                    <input type="hidden" name="user_role" value="parent">
                    
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="firstName" required>
                
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="lastName" required>
                
                    <label for="contact-number">Contact Number:</label>
                    <input type="text" id="contact-number" name="contactNumber" placeholder="0712345678" required>
                    <span id="contact-error" style="color: red; font-size: 14px; display: none;">Invalid contact number format</span>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                
                    <label for="nic">NIC:</label>
                    <input type="text" id="nic" name="nic" required>
                    <span id="nic-error" style="color: red; font-size: 14px; display: none;">Invalid NIC </span>
                
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

    <script>
    document.getElementById('signupForm').addEventListener('submit', function (e) {
        const contactNumber = document.getElementById('contact-number').value;
        const contactError = document.getElementById('contact-error');
        const nicInput = document.getElementById('nic');
        const nicError = document.getElementById('nic-error');

        // Regex pattern to validate contact number
        const contactPattern = /^\d{10}$/; // Example: 712345678

        // Validate the contact number
        if (!contactPattern.test(contactNumber)) {
            contactError.style.display = 'block';
            e.preventDefault(); // Prevent form submission
        } else {
            contactError.style.display = 'none';
        }

        // Validate NIC (must be exactly 12 digits)
        const nicPattern = /^\d{12}$/;
        if (!nicPattern.test(nicInput.value)) {
            nicError.style.display = 'block';
            e.preventDefault(); // Prevent form submission
        } else {
            nicError.style.display = 'none';
        }
    });

    // Toggle visibility for the password fields
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bxs-lock-alt');
        this.classList.toggle('bxs-lock-open-alt');
    });

    const toggleReEnterPassword = document.getElementById('toggleReEnterPassword');
    const reEnterPasswordInput = document.getElementById('reEnterPassword');
    toggleReEnterPassword.addEventListener('click', function () {
        const type = reEnterPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        reEnterPasswordInput.setAttribute('type', type);
        this.classList.toggle('bxs-lock-alt');
        this.classList.toggle('bxs-lock-open-alt');
    });
    </script>
</body>
</html>
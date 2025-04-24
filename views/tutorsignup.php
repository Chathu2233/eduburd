<?php
session_start();

require 'db.php';
require '../vendor/autoload.php';
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
    $years_of_experience = $_POST['years_of_experience'];

    // Handle CV file upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $cv_tmp_path = $_FILES['cv']['tmp_name'];
        $cv_name = basename($_FILES['cv']['name']);
        $cv_upload_path = __DIR__ . '/uploads/' . $cv_name;

        if (!move_uploaded_file($cv_tmp_path, $cv_upload_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload CV']);
            exit;
        }
    }

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

        // Insert into tutor table with the user_id as foreign key
        $stmt = $pdo->prepare("INSERT INTO tutor (user_id, years_of_experience, cv) 
                               VALUES (:user_id, :years_of_experience, :cv)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':years_of_experience', $years_of_experience);
        $stmt->bindParam(':cv', $cv_upload_path);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Send verification email
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
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email';
            $mail->Body    = "Hi $first_name,<br><br>Please verify your email by clicking the link below:<br>
                              <a href='http://localhost/eduburd/views/verify.php?code=$verification_code'>Verify Email</a><br><br>Thank you!";

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        echo "<script>
                alert('Registration successful! Please check your email to verify your account.');
                window.location.href = 'login.php';
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
    <title>Tutor Sign Up</title>
    <link rel="stylesheet" href="../assets/css/signup.css">

    <!-- Font and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include 'header_guest.php'; ?>
    </header>

    <!-- Sign Up Form Section -->
    <main>
        <div class="signup-container">
            <div class="signup-form">
                <h3>Tutor Signup</h3>
                <form id="signupForm" action="tutorsignup.php" method="post" enctype="multipart/form-data">
                    <!-- Hidden field to store the role -->
                    <input type="hidden" name="user_role" value="tutor">
                    
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="firstName" required>
                
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="lastName" required>
                
                    <label for="contact-number">Contact Number:</label>
                    <input type="text" id="contact-number" name="contactNumber" required>
                
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                    <script>
                        // Set the maximum date for the DOB field to today's date
                        const dobInput = document.getElementById('dob');
                        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
                        dobInput.setAttribute('max', today); // Set the max attribute to today's date
                    </script>
                
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                
                    <label for="reEnterPassword">Re-enter Password:</label>
                    <input type="password" id="reEnterPassword" name="reEnterPassword" required>
                
                    <label for="years_of_experience">Years of Experience:</label>
                    <input type="text" id="years_of_experience" name="years_of_experience" required>
                
                    <label for="cv-upload">Upload Your CV:</label>
                    <input type="file" id="cv-upload" name="cv" accept=".pdf,.doc,.docx" required>
                
                    <div class="form-buttons">
                        <button type="submit" class="submit-btn">Sign Up</button>
                        <button type="reset" class="cancel-btn">Reset</button>
                    </div>
                </form>
            </div>
            <div class="already-account">
                <p>Already have an account?</p>
                <a href="login.php" class="login-large-btn">Login</a>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <?php include 'footer.php'; ?>
</body>
</html>
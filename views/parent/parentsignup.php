<!-- filepath: c:\xampp\htdocs\eduburd\views\parent\parentsignup.php -->
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
    $country_code = $_POST['countryCode'];
    $contact_no = $country_code . $_POST['contactNumber'];
    $password = $_POST['password'];
    $re_password = $_POST['reEnterPassword'];

    // Parent-specific data
    $nic = $_POST['nic'];

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
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
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
                
                    
                    <div class="contact-number-container">
    <label for="contact-number">Contact Number:</label>
    <div class="contact-details" style="display: flex; align-items: center; gap: 10px; flex: 1;">
        <!-- Dropdown for country codes -->
        <select id="country-code" name="countryCode" required style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; width: 120px;">
            <option value="+94">+94 (Sri Lanka)</option>
            <option value="+91">+91 (India)</option>
            <option value="+44">+44 (UK)</option>
            <option value="+1">+1 (USA)</option>
            <option value="+1">+1 (Canada)</option>
            <option value="+61">+61 (Australia)</option>
        </select>
     </div>
     <div>
           <!-- Input for contact number -->
        <input type="text" id="contact-number" name="contactNumber" placeholder="712345678" required style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
    </div>
    <span id="contact-error" style="color: red; font-size: 14px; display: none;">Invalid contact number format</span>
</div>

<label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                
                    <label for="nic">NIC:</label>
                    <input type="text" id="nic" name="nic" required>
                
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                
                    <label for="reEnterPassword">Re-enter Password:</label>
                    <input type="password" id="reEnterPassword" name="reEnterPassword" required>
                
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
        const countryCode = document.getElementById('country-code').value;
        const contactNumber = document.getElementById('contact-number').value;
        const contactError = document.getElementById('contact-error');

        // Combine country code and contact number
        const fullContactNumber = countryCode + " " + contactNumber;

        // Regex pattern to validate full contact number
        const pattern = /^\+(\d{1,3})\s?\d{9,10}$/; // Example: +94 712345678

        // Validate the full contact number
        if (!pattern.test(fullContactNumber)) {
            contactError.style.display = 'block';
            e.preventDefault(); // Prevent form submission
        } else {
            contactError.style.display = 'none';
        }
    });
</script>
</body>
</html>
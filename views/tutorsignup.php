<?php
require_once 'constants.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Sign up</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/signup.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/header_guest.css">

    <!-- js file link  -->
    <script src="<?php echo ROOT; ?>/assets/js/password.js" defer></script>
    <script src="<?php echo ROOT; ?>/assets/js/filesize.js" defer></script>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    // Retrieve and sanitize input data for the user
    $user_role = $_POST['user_role'];
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $contact_no = $_POST['contactNumber'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $re_password = $_POST['reEnterPassword'];

    // Tutor-specific data
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
        // Insert into user table
        $stmt = $pdo->prepare("INSERT INTO user (user_role, first_name, last_name, email, contact_no, dob, password) 
                               VALUES (:user_role, :first_name, :last_name, :email, :contact_no, :dob, :password)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact_no', $contact_no);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':user_role', $user_role);
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

        // Automatically log the user in by setting session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_role'] = $user_role;
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $first_name;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    echo "<script>
                alert('Registration successful');
                window.location.href = 'login.php';
              </script>";
    exit;
}
?>
    <!-- Header Section -->
    <header>
        <?php
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include 'header_admin.php';
                    break;
                case 'student':
                    include 'header_student.php';
                    break;
                case 'tutor':
                    include 'header_tutor.php';
                    break;
                case 'parent':
                    include 'header_parent.php';
                    break;
                default:
                    include 'header_guest.php'; // Fallback for unknown roles
            }
        } else {
            include 'header_guest.php'; // For guests (not logged in)
        }
        ?>
    </header>

    <!-- Sign up form Section -->
    <main>
    <div class="signup-container">
        <div class="signup-form">
            <h3>Tutor Signup</h3>
            <form action="" method="POST" enctype="multipart/form-data" id="signupForm" onsubmit="return validateForm()">
                <input type="hidden" name="user_role" value="tutor">
                <label for="first-name">First Name :</label>
                <input type="text" id="first-name" name="firstName" 
                pattern="^[A-Za-z]+$" title="Only alphabets are allowed"
                required>
                <label for="last-name">Last Name :</label>
                <input type="text" id="last-name" name="lastName" 
                pattern="^[A-Za-z]+$" title="Only alphabets are allowed"
                required>
                <label for="contact-number">Contact Number :</label>
                <input type="text" id="contact-number" name="contactNumber"
                pattern ="^\+?[1-9]\d{1,14}$" title="Please enter a valid phone number"
                required>
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" 
                minlength="8" maxlength="20"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                title="Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character."
                required>
                <label for="reEnterPassword">Re-enter Password:</label>
                <input type="password" id="reEnterPassword" name="reEnterPassword" required>
                <label for="years_of_experience">Years of Experience :</label>
                <input type="text" id="years_of_experience" name="years_of_experience" required>
                <label for="cv-upload">Upload Your CV:</label>
                <input type="file" id="cv-upload" name="cv" accept=".pdf,.doc,.docx" required>
                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Submit</button>
                    <button type="reset" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
        <div class="already-account">
            <p>Already Have An Account?</p>
            <a href="<?php echo ROOT; ?>/views/login.php" class="login-large-btn">Login</a>
        </div>
        </div>
    </main>

    <!-- Footer Section -->
    <?php include 'footer.php'; ?>
</body>
</html>

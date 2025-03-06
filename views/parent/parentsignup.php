<?php

session_start();

require '../db.php';

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

        // Insert into parent table with the user_id as foreign key
        $stmt = $pdo->prepare("INSERT INTO parent (user_id, nic) 
                               VALUES (:user_id, :nic)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':nic', $nic);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        


        // Automatically log the user in by setting session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_role'] = $user_role;
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $first_name;

        
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }

    echo "<script>
                alert('Registration successful');
                window.location.href = '../login.php';
              </script>";
        exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Sign Up</title>
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
                <h3>Parent Signup</h3>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form id="signupForm" action="parentsignup.php" method="post">
                    <!-- Hidden field to store the role -->
                    <input type="hidden" name="user_role" value="parent">
                    
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="firstName" 
                    pattern="^[A-Za-z]+$" title="Only alphabets are allowed"
                    required>
                
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="lastName" 
                    pattern="^[A-Za-z]+$" title="Only alphabets are allowed"
                    required>
                
                    <label for="contact-number">Contact Number:</label>
                    <input type="text" id="contact-number" name="contactNumber" 
                    pattern ="^\+?[1-9]\d{1,14}$" title="Please enter a valid phone number"
                    required>
                
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                
                    <label for="nic">NIC:</label>
                    <input type="text" id="nic" name="nic" required>
                
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" 
                    minlength="8" maxlength="20"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                    title="Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character."
                    required>
                
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
 


</body>
</html>

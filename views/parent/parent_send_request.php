<?php
session_start();
include '../connect.php';


$error_message = ''; // Variable to store error messages
$success_message = ''; // Variable to store success messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['studentid'];
   // $parent_id = 1; // Replace with actual logged-in parent ID, e.g., $_SESSION['parent_id']

    // Check if the student ID exists in the student table
    $check_student_query = "SELECT student_id FROM student WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $check_student_query);

    if (mysqli_num_rows($result) > 0) {
        // If the student exists, insert the request
        $query = "INSERT INTO parent_student_request ( student_id, date, status) 
                  VALUES ( $student_id, NOW(), 'Pending')";
        if (mysqli_query($conn, $query)) {
            $success_message = "Request sent successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        // If the student doesn't exist, show an error message
        $error_message = "Error: Invalid format";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child</title>
    <link rel="stylesheet" href="../../assets/css/parent/parent_send_request.css">
    
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>
    

    <!-- Form Section -->
    <form method="POST" action="">
        <h2>Add Your Child</h2>
        <label for="studentid">Student ID:</label>
        <input type="text" name="studentid" id="studentid" placeholder="Enter Student ID" required>
        <button type="submit">Send Request</button>
    </form>

    <!-- Messages -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
    
</body>
</html>

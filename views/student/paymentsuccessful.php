<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Modern Font -->
    <link rel="stylesheet" href="../../assets/css/student/paymentsuccessful.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="icon">
            <img src="../../assets/images/paysuccess.gif" alt="Payment Success Animation">
        </div>
        <h1>Payment Successful</h1>
        <p>Your payment was processed successfully. We will reach out to you with further details soon!</p>
        <a href="stu_dashboard.php" class="btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
session_start();
require_once 'constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Menu - EduBurd</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/signupmenu.css">
</head>
<body>

    <!-- Header Section -->

    <header class="navbar">
        <?php include 'header_guest.php'; ?>
    </header>

    <div class="container">
    <div class="actor-container">
        <!-- Student Box -->
        <a href="<?php echo ROOT; ?>/views/student/studentsignup.php" class="actor-box student-box">
            <h2>Student</h2>
        </a>

        <!-- Parent Box -->
        <a href="<?php echo ROOT; ?>/views/parent/parentsignup.php" class="actor-box parent-box">
            <h2>Parent</h2>
        </a>

        <!-- Tutor Box -->
        <a href="<?php echo ROOT; ?>/views/tutorsignup.php" class="actor-box tutor-box">
            <h2>Tutor</h2>
        </a>
    </div>
    </div>
    <?php include 'footer.php'; ?> 
        
</body>
</html>

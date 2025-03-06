<?php require_once 'constants.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/header_student.css">

</head>
<body>

<header>
        <div class="logo">
            <img src="<?php echo ROOT; ?>/assets/images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo">     
        </div>
        <nav>
            <ul>
                <li><a href="<?php echo ROOT; ?>/views/home.php">Home</a></li>
                <li><a href="<?php echo ROOT; ?>/views/findatutor.php">Find A Tutor</a></li>
                <li><a href="<?php echo ROOT; ?>/views/tutorsignup.php">Become A Tutor</a></li>
                <li><a href="<?php echo ROOT; ?>/views/aboutus.php">About Us</a></li>
                <li><a href="<?php echo ROOT; ?>/views/student/stu_dashboard.php">My Dashboard</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <a href="<?php echo ROOT; ?>/views/logout.php" class="login-btn">Logout</a>
        </div>
</header>

</body>
</html>
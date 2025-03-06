<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../../assets/css/student/myprofile.css">
</head>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_parent.php'; ?>
    </header>
<body>
<div class="content">
    <div class="profile-container">
        <div class="profile-card">
            <img src="https://via.placeholder.com/150" alt="Profile Picture" class="profile-pic">
            <h1 class="student-name">Lakshan</h1>
            <p class="student-info">ID: 12345678</p>
            <p class="student-info">Email: johndoe@example.com</p>
            <p class="student-info">Parent Name: Jack</p>
            <p class="student-info">Grade: Beginner</p>
            
        </div>
    </div>
</div>
    <?php include '../footer.php'; ?>  
</body>
</html>

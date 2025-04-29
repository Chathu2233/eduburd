<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Parent</title>
    <link rel="stylesheet" href="../../assets/css/student/myparent.css">
  
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Parent Content -->
        <main class="dashboard">
       

          
   <!-- filepath: c:\xampp\htdocs\eduburd\views\student\myparent.php -->
<div class="parent-grid">
    <!-- View Parent Request Card -->
    <a href="view_request.php" class="parent-box-link">
        <div class="parent-box">
            <h3 class="parent-title">View Parent Request</h3>
            <p class="parent-description">View your parent request status.</p>
        </div>
    </a>

    <!-- View Parent Profile Card -->
    <a href="parentlist.php" class="parent-box-link">
        <div class="parent-box">
            <h3 class="parent-title">View Parent Profile</h3>
            <p class="parent-description">View your parent profile details.</p>
        </div>
    </a>
</div>

</section>

        </main>


      


    </div>
    
    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

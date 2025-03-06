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
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
<body>
    <div class="content">
    <div class="container">
        <h1>My Parent</h1>
        <div class="button-container">
            <!-- View Parent Request -->
            <a href="view_request.php" class="btn view-request-btn">View Parent Request</a>

            <!-- View Parent Profile -->
            <a href="viewparent.php" class="btn view-profile-btn">View Parent Profile</a>
        </div>
    </div>
    </div>
    <?php include '../footer.php'; ?> 
</body>
</html>

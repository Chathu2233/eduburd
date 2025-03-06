<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/student/stu_dashboard.css"> 
    
    
</head>
<body>
     <!-- Header Section -->
     <header>
        <?php
        include '../header_student.php';
        ?>
    </header>
    <!-- Sidebar -->
    <div class="container">
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Dashboard Logo" width="50" height="50" style="margin-top: 30px;">
        <ul>
            <div class="sidebar1">
                <li><a href="myprofile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </div>
            <div class="sidebar2">
                <li><a href="stu_dashboard.php"><i class="fas fa-tachometer-alt"></i> My Subjects</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="myparent.php"><i class="fas fa-user"></i> My Parent</a></li>
            </div>
            <div class="sidebar4">
                <li><a href="resourcelibrary.php"><i class="fas fa-book"></i> Resource Library</a></li>
            </div>
            <div class="sidebar5">
                <li><a href="viewannouncement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            </div>
            <div class="sidebar6">
                <li><a href="paymentinfo.php"><i class="fas fa-credit-card"></i> Payment Info</a></li>
            </div>
            <div class="sidebar7">
                <li><a href="editprofile.php"><i class="fas fa-edit"></i> Edit Profile</a></li>
            </div>
        </ul>
    </div>

    <main class="dashboard">
            <section class="welcome">
            <h1>WELCOME BACK, STUDENT!</h1>
            <p>Always Stay Updated In Your Student Portal</p>
            </section>

    
        
        <!-- Enrolled Courses Section -->
        <section class="enrolled-courses">
            <h2>Enrolled Courses</h2>
            <div class="courses">
                <div class="course"><a href="tutor.php">Course 1</a></div>
                <div class="course"><a href="tutor.php">Course 2</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
                <div class="course"><a href="tutor.php">Course 2</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
                <div class="course"><a href="tutor.php">Course 3</a></div>
            </div>
        </section>
    </main>
    </div>
    <?php include '../footer.php'; ?>  
</body>
</html>


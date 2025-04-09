<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/student/stu_dashboard.css"> 
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Dashboard Content -->
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

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>


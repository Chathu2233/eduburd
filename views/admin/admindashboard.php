<?php

    session_start();
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin/admindashboard.css"> <!-- Link to CSS file -->
</head>
<body>


      <header >
    <?php
    include '../header_admin.php'
    ?>
    </header>

<!-- HTML Structure -->
<div class="content-wrapper">
 
    <!-- Main Dashboard Content -->
    <div class="dashboard-main">
        <div class="welcome-message">
            <h1>Welcome Back, Admin!</h1>
            <p>Manage your platform effectively.</p>
        </div>
        <div class="dashboard-cards">
            <!-- Dashboard Management Cards -->
            <div class="card manage-tutors">
                <h3>Manage Tutors</h3>
                <p>View, add, edit, or delete tutors.</p>
                <a href="managetutors.php" class="button">Go to Tutors</a>
            </div>
            <div class="card manage-students">
                <h3>Manage Students</h3>
                <p>View, add, edit, or delete students.</p>
                <a href="managestudents.php" class="button">Go to Students</a>
            </div>
            <div class="card manage-parents">
                <h3>Manage Parents</h3>
                <p>View, add, edit, or delete parents.</p>
                <a href="manageparents.php" class="button">Go to Parents</a>
            </div>
            <div class="card manage-courses">
                <h3>Manage Courses</h3>
                <p>View, add, edit, or delete courses.</p>
                <a href="managecourses.php" class="button">Go to Courses</a>
            </div>
            <div class="card manage-announcements">
                <h3>Manage Announcements</h3>
                <p>Post new announcements for students and tutors.</p>
                <a href="announcements.php" class="button">Go to Announcements</a>
            </div>
            <div class="card manage-payments">
                <h3>Manage Payments</h3>
                <p>View and track payments and transactions.</p>
                <a href="managepayments.php" class="button">Go to Payments</a>
            </div>
            <div class="card view-analytics">
                <h3>View Analytics</h3>
                <p>Analyze platform usage and trends.</p>
                <a href="viewanalytics.php" class="button">Go to Analytics</a>
                
            </div>
            <div class="card manage-settings">
                <h3>Settings</h3>
                <p>Configure platform settings and preferences.</p>
                <a href="settings.php" class="button">Go to Settings</a>
            </div>
            <div class="card manage-settings">
                <h3>Resource Library</h3>
                <p>Overlook and track about Resources.</p>
                <a href="../resourcelibrary.php" class="button">Explore Resources</a>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>




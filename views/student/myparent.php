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
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
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
            <section class="parent-section">
                <h1>My Parent</h1>
                <p class="description">Manage your parent requests and view parent profiles here.</p>
                <div class="button-container">
                    <!-- View Parent Request -->
                    <a href="viewrequest.php" class="btn view-request-btn">View Parent Request</a>

                    <!-- View Parent Profile -->
                    <a href="viewparent.php" class="btn view-profile-btn">View Parent Profile</a>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

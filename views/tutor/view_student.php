<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/view_student.css">
<body>
    <!-- Header Section -->
    <header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <!-- Student Profile Section -->
    <main class="profile-container">
        <section class="profile-card">
            <img src="../../assets/images/student.jpg" alt="Student Avatar" class="profile-avatar">
            <h2 class="student-name">Ayathma Amanethmi</h2>
            <p class="student-grade">Grade: 10</p>
            <p class="student-email"><strong>Email:</strong> amaamanethmi@gmail.com</p>
            <p class="student-contact"><strong>Contact:</strong> +94 705043255</p>
            <div class="student-bio">
                <h3>About Ayathma:</h3>
                <p>Ayathma is an enthusiastic student with a passion for mathematics and science. He enjoys participating in group projects and is looking forward to improving his skills through online tutoring.</p>
            </div>
            <div class="action-buttons">
                <a href="student_request.php" class="btn back-btn">Back</a>
            </div>
        </section>
    </main>
    <?php include '../footer.php'; ?>
</body>
</html>

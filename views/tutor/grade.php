<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/grade.css">
</head>
<body>

<header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <section class="subjects">
        <h1>Grades</h1>
        <a href="addgrade.php"><button class="add-subjects-btn">+Add Grade</button></a>
        <div class="subjects-grid">
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 05</p></div></a>
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 06</p></div></a>
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 07</p></div></a>
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 08</p></div></a>
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 09</p></div></a>
            <a href="myclass.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Grade 10</p></div></a>
        </div>
    </section>

  <!-- Footer Section -->
  <?php include '../footer.php'; ?>

</body>
</html>

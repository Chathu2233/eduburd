<?php
session_start();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/tutor/subject.css">
    <link rel="stylesheet" href="../../assets/css/tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>
<header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <section class="subjects">
        <h1>My Subjects</h1>
        <a href="addsubject.php"><button class="add-subjects-btn">+Add Subjects</button></a>
        <div class="subjects-grid">
            <a href="grade.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Science</p></div></a>
            <a href="grade.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Mathematics</p></div></a>
            <a href="grade.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Sinhala</p></div></a>
            <a href="grade.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>English</p></div></a>
            <a href="grade.php"><div class="subject-card"><span class="delete-icon">🗑️</span><p>Econ</p></div></a>
        </div>
    </section>

    <?php include '../footer.php'; ?>

</body>
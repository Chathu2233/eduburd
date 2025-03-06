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
    <link rel="stylesheet" href="../../assets/css/Tutor/myclass.css">
</head>
<body>

<header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <section class="subjects">
    <h1>My Classes</h1>
    <div class="subjects-grid">
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Ayathma" class="subject-img">
            <p class="subject-name">Ayathma</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Chathumini" class="subject-img">
            <p class="subject-name">Chathumini</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student2.jpg" alt="Farshad" class="subject-img">
            <p class="subject-name">Farshad</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Sajidha" class="subject-img">
            <p class="subject-name">Sajidha</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Geeradha" class="subject-img">
            <p class="subject-name">Geeradha</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Hansika" class="subject-img">
            <p class="subject-name">Hansika</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student.jpg" alt="Sachini" class="subject-img">
            <p class="subject-name">Sachini</p>
        </a>
        <a href="classschedule.php" class="subject-card">
            <img src="../../assets/images/student2.jpg" alt="Safran" class="subject-img">
            <p class="subject-name">Safran</p>
        </a>
    </div>
</section>


<?php include '../footer.php'; ?>

</body>
</html>

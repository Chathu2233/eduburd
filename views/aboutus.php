<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Tutor/beforelogin_header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/aboutus.css">
</head>
<body>
<header>
        <?php
        // Dynamically include the correct header based on user role
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'admin':
                include 'header_admin.php';
                break;
            case 'student':
                echo "Loading student header...";
                include 'header_student.php';
                break;
            case 'tutor':
                include 'header_tutor.php';
                break;
            case 'parent':
                include 'header_parent.php';
                break;
            default:
                include 'header_guest.php'; // Fallback for unknown roles
        }
    } else {
        include 'header_guest.php'; // For guests (not logged in)
    }
?>
    </header>

    <section class="hero">
        <div class="hero-content">
            <img src="../assets/images/aboutus.png" alt="About Us">
            <div class="stats">
                <p>"We're only just getting started on our journey"</p>
                <div class="stats-grid">
                    <div class="stat"><strong>500+</strong> Lessons</div>
                    <div class="stat"><strong>100+</strong> Students</div>
                    <div class="stat"><strong>90%</strong> Sessions Rated 4/5</div>
                    <div class="stat"><strong>95%</strong> Success Rate</div>
                </div>
            </div>
        </div>
    </section>

    <section class="why-choose-us">
        <h2>Why Choose Us?</h2>
        <p>
            At Edbubrd, we offer personalized learning with expert tutors, flexible scheduling, and a wide range of subjects to help you succeed. Our secure and easy-to-use platform ensures you get the support and tools you need to reach your goals. Join us today and experience the difference!
        </p>
    </section>

    <section class="vision-mission">
        <div class="vision">
            <h3>Our Vision</h3>
            <p>
                We aim to provide all students with high-quality education, enabling them to become our future-oriented global citizens.
            </p>
        </div>
        <div class="mission">
            <h3>Our Mission</h3>
            <p>
                We aim to provide a safe learning environment for every student to become self-aware and develop a sense of responsibility towards the world.
            </p>
        </div>
    </section>

    
        <?php include 'footer.php'; ?>
</body>
</html>

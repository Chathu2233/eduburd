<?php
session_start();
require_once 'constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/home.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <?php
        // Dynamically include the correct header based on user role
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include __DIR__ . '/header_admin.php';
                    break;
                case 'student':
                    include __DIR__ . '/header_student.php';
                    break;
                case 'tutor':
                    include __DIR__ . '/header_tutor.php';
                    break;
                case 'parent':
                    include __DIR__ . '/header_parent.php';
                    break;
                default:
                    include __DIR__ . '/header_guest.php'; // Fallback for unknown roles
            }
        } else {
            include __DIR__ . '/header_guest.php'; // For guests (not logged in)
        }
        ?>
    </header>
   
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <img src="<?php echo ROOT; ?>/assets/images/HERO BANNER.png" alt="EduBurd Hero Banner">
        </div>
    </section>

    <div class="content-wrapper">
        <!-- Offer Section -->
        <section class="what-we-offer">
            <h2>What We Offer</h2>
            <div class="offer-grid">
                <div class="offer-item" id="1">One-to-One Personalized Tutoring</div>
                <div class="offer-item" id="2">Live Classes</div>
                <div class="offer-item" id="3">Expert Handpicked Teachers</div>
                <div class="offer-item" id="4">Parental Dashboard</div>
                <div class="offer-item" id="5">Learn From Anywhere</div>
                <div class="offer-item" id="6">Feasible Booking Schedules</div>
                <div class="offer-item" id="7">Progress Tracking</div>
                <div class="offer-item" id="8">Resource Library</div>
                <div class="offer-item" id="9">Discussion Forums</div>
            </div>
            <div class="offer-buttons">
                <a href="<?php echo ROOT; ?>/views/findatutor.php" class="btn">Find Your Tutor</a>
                <a href="<?php echo ROOT; ?>/views/parent/parentsignup.php" class="btn">Track Your Child's Progress</a>
                <a href="<?php echo ROOT; ?>/views/tutor/tutorsignup.php" class="btn">Become A Tutor</a>
            </div>
        </section>

        <!-- Student Section -->
        <section class="you-are">
            <h2>You Are</h2>
            <div class="student-grid">
                <div class="student-item" id="1">Primary Student</div>
                <div class="student-item" id="2">Lower Secondary</div>
                <div class="student-item" id="3">IGCSE</div>
                <div class="student-item" id="4">AS & A2</div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <div class="stat-item">20+ Subjects</div>
            <div class="stat-item">20+ Tutors</div>
            <div class="stat-item">100+ Students</div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials">
            <h2>What Our Students Say</h2>
            <div class="testimonial-slider">
                <div class="testimonial-item">
                    <p>"The platform is so easy to use, and I love being able to choose teachers who match my learning style."</p>
                    <h4>- Godfri</h4>
                </div>
                <div class="testimonial-item">
                    <p>"The teachers on Eduburd are amazing! They explain concepts so well, and I can always ask questions without feeling nervous."</p>
                    <h4>- Vee</h4>
                </div>
                <div class="testimonial-item">
                    <p>"My mom says she's never seen me this excited about studying before Eduburd. I think she's right!"</p>
                    <h4>- Yoosuf</h4>
                </div>
            </div>
        </section>

        <!-- Free Resources Section -->
        <section class="free-resources">
            <div class="library">
                <div class="text">
                    <p>Free<br>Study<br>Resources</p>
                </div>
                <a href="<?php echo ROOT; ?>/views/resourcelibrary.php" class="btn">Explore Resources</a>
            </div>
        </section>
    </div>

    <!-- Footer Section -->
    <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
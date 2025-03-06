<?php
session_start();
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
    <link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>

    <!-- Header Section -->
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
   
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <img src="../assets/images/HERO BANNER.png" alt="EduBurd Hero Banner">
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
                <a href="findatutor.php" class="btn">Find Your Tutor</a>
                <a href="parent/parentsignup.php" class="btn">Track Your Child's Progress</a>
                <a href="tutor/tutorsignup.php" class="btn">Become A Tutor</a>
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
                <a href="resourcelibrary.php" class="btn">Explore Resources</a>
            </div>
        </section>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <!-- Logo and Description -->
            <div class="footer-logo">
                <img src="../assets/images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo">
                <p>Empowering learners with top-quality tutoring across a variety of subjects and levels. Join us to enhance your learning journey.</p>
            </div>

            <!-- Footer Links -->
            <div class="footer-links">
                <div class="footer-section">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="/contact">Contact Us</a></li>
                        <li><a href="/aboutus">About Us</a></li>
                    
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Programs</h4>
                    <ul>
                        <li><a href="/programs/primary">Primary</a></li>
                        <li><a href="/programs/secondary">Secondary</a></li>
                        <li><a href="/programs/igcse">IGCSE</a></li>
                        <li><a href="/programs/as&a2">AS & A2</a></li>
                    </ul>
                </div>
                <div class="footer-section" id="contact">
                    <h4>Contact</h4>
                    <ul>
                        <li>Address: UCSC Building Complex, 35 Reid Ave, Colombo 00700 </li>
                        <li>Email: support@eduburd.com</li>
                        <li>Phone: +94 761 166 329</li>
                    </ul>
                </div>

                <!-- Social Media Links -->
                <div class="footer-social">
                    <a href="#"><img src="../assets/images/facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="../assets/images/twitter.png" alt="Twitter"></a>
                    <a href="#"><img src="../assets/images/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="../assets/images/linkedin.png" alt="LinkedIn"></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2024 EduBurd | All Rights Reserved</p>
        </div>
    </footer>

</body>
</html>

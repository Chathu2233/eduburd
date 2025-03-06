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
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/subjectweoffer.css">
    
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
                include 'header_parent2.php';
                break;
            default:
                include 'header_guest2.php'; // Fallback for unknown roles
        }
    } else {
        include 'header_guest.php'; // For guests (not logged in)
    }
?>
    </header>

 <!-- Content Section -->
 <div class="content-wrapper">
    <!-- Page Breadcrumb -->
    <div class="breadcrumb">
        <p>Homepage &gt; Subjects</p>
    </div>
    
    <!-- Subjects We Offer Section -->
    <section class="subjects-section">
        <h1>Subjects We Offer,</h1>
    </section>

    <!-- Tuition Sections -->
    <section class="tuition-section">
        <h2>Personalised Primary And Lower Secondary Tuition</h2>
        <div class="tuition-content">
            <div class="subject-card">
                <img src="../assets/images/primary.webp" alt="Primary" class="subject-image">
            </div>
            <div class="subject-card">
                <p>Foundational subjects to build essential skills and foster curiosity.
                <ul>
                    <li>English</li>
                    <li>Mathematics</li>
                    <li>Science</li>
                    <li>Social Studies</li>
                    <li>ICT</li>
                </ul></p>
            </div>
        </div>
        <button class="search-button"><a href="findatutor.php">Search For Tutors</a> </button>
    </section>

    <section class="tuition-section">
        <h2>Personalised IGCSE (International General Certificate of Secondary Education) Tuition</h2>
        <div class="tuition-content">
            <div class="subject-card">
                <p>Comprehensive subjects that develop analytical and academic skills in preparation for advanced studies.
                    <ul>
                        <li>English Language and Literature</li>
                        <li>Mathematics</li>
                        <li>Physics</li>
                        <li>Chemistry</li>
                        <li>Biology</li>
                        <li>Business Studies</li>
                        <li>ICT</li>
                    </ul></p>
            </div>
            <div class="subject-card">
                <img src="../assets/images/igcse.webp" alt="IGCSE" class="subject-image">
            </div>
        </div>
        <button class="search-button"><a href="findatutor.php">Search For Tutors</a> </button>
    </section>

    <section class="tuition-section">
        <h2>Personalised A1 & A2 (Advanced Level - Years 1 & 2) Tuition</h2>
        <div class="tuition-content">
            <div class="subject-card">
                <img src="../assets/images/A2.webp" alt="A2" class="subject-image">
            </div>
            <div class="subject-card">
                <p>In-depth courses designed for specialization and preparation for higher education.
                <ul>
                    <li>English Literature</li>
                    <li>Mathematics</li>
                    <li>Physics</li>
                    <li>Chemistry</li>
                    <li>Biology</li>
                    <li>Economics</li>
                    <li>Business Studies</li>
                    <li>Psychology</li>
                </ul></p></div>
        </div>
        <button class="search-button"><a href="findatutor.php">Search For Tutors</a> </button>
    </section>

 </div>
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
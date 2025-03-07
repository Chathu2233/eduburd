<?php
session_start();
require_once '../constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Each Child Dashboard</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/eachchild_dashboard.css">
</head>
<body>
    <!--header-->
<header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout Container -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>

        <!-- Main Content: Child's Enrolled Subjects -->
        <div class="container">
            <h1>Your child's enrolled subjects</h1>
            <p class="description">As a parent, you can review the subjects your child has enrolled in for the academic year.</p>
            <ul id="subjectsList">
                <li>
                    <div class="subject-content">
                        <img src="<?php echo ROOT; ?>/assets/images/english.png" alt="English Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>English</span>
                            <p class="subject-desc">Develop your child's language skills, including grammar, vocabulary, and reading comprehension.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
                <li>
                    <div class="subject-content">
                        <img src="<?php echo ROOT; ?>/assets/images/maths.jfif" alt="Math Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>Math</span>
                            <p class="subject-desc">Help your child master mathematical concepts, from algebra to calculus.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
                <li>
                    <div class="subject-content">
                        <img src="<?php echo ROOT; ?>/assets/images/science.jfif" alt="Science Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>Science</span>
                            <p class="subject-desc">Explore physics, chemistry, and biology with hands-on experiments and theories.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Footer -->
      <!-- Footer -->
      <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
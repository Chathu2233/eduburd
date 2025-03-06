<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child's Enrolled Subjects</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="css/subjectsenrolled.css">
</head>
<body>
    <!-- Header -->
    <<header>
        <?php include '../header_parent.php'; ?>
    </header>

    <div class="container">
        <h1 class="main-title">Your Child's Enrolled Subjects</h1>
        <p class="description">Easily explore and manage the subjects your child is currently enrolled in. Click "View Details" for additional information about each subject.</p>

        <div class="subjects-list">
            <!-- Subject Card -->
            <div class="subject-card">
                <img src="images/english.png" alt="English Icon" class="subject-icon">
                <div class="subject-details">
                    <h3 class="subject-name">English</h3>
                    <p class="subject-desc">Develop your child's language skills, including grammar, vocabulary, and reading comprehension.</p>
                </div>
                <a href="seetutor.html" class="view-details-btn">View Details</a>
            </div>

            <!-- Subject Card -->
            <div class="subject-card">
                <img src="images/english.png" alt="Math Icon" class="subject-icon">
                <div class="subject-details">
                    <h3 class="subject-name">Math</h3>
                    <p class="subject-desc">Help your child master mathematical concepts, from algebra to calculus. And hands on experiments.</p>
                </div>
                <a href="math-details.html" class="view-details-btn">View Details</a>
            </div>

            <!-- Subject Card -->
            <div class="subject-card">
                <img src="images/english.png" alt="Science Icon" class="subject-icon">
                <div class="subject-details">
                    <h3 class="subject-name">Science</h3>
                    <p class="subject-desc">Explore physics, chemistry, and biology with hands-on experiments and theories.</p>
                </div>
                <a href="science-details.html" class="view-details-btn">View Details</a>
            </div>
        </div>
    </div>
      <!-- Footer -->
      <?php include '../footer.php'; ?>
</body>
</html>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/announcement.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>

   <!-- Header Section -->
   <header>
   <?php
    include '../header_tutor.php'
    ?>
    </header>

   <!-- Announcements Title Section -->
<section class="announcement-title">
    <h1>📢 Announcements</h1>
    <p>Stay updated with the latest news and important information regarding our platform.</p>
</section>

<!-- Announcements Section -->
<main class="announcement-container">
    <div class="announcement">
        <h2>🎉 New Feature: Class Scheduling</h2>
        <p>We have introduced a new class scheduling feature for tutors and students. Check it out in the "Schedule" section!</p>
        <span class="date">Posted on: 16th November 2024</span>
    </div>
    <div class="announcement">
        <h2>⚙️ Maintenance Alert</h2>
        <p>Our system will undergo maintenance on 20th November 2024 from 12:00 AM to 6:00 AM. Please plan accordingly.</p>
        <span class="date">Posted on: 15th November 2024</span>
    </div>
    <div class="announcement">
        <h2>🎄 Holiday Notice</h2>
        <p>Classes will not be held on 25th December 2024 for the Christmas holiday. Happy holidays!</p>
        <span class="date">Posted on: 14th November 2024</span>
    </div>
</main>


<?php include '../footer.php'; ?>

</body>
</html>

<?php
session_start();
require '../db.php'; // Include the database connection
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
    <link rel="stylesheet" href="../../assets/css/Tutor/view_announcement.css"> <!-- Link to your CSS file -->
</head>
<body>

   <!-- Header Section -->
   <header>
   <?php include '../header_tutor.php'; ?>
   </header>

   <div class="container">
        <?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

    <div class="content-section">
        <section class="announcement-title">
            <h1>📢 Announcements</h1>   
            <p>Stay updated with the latest news and important information regarding our platform.</p>
        </section>
    <main class="announcement-container">
    <?php
    try {
        $stmt = $pdo->prepare("
            SELECT 
                admin_announcement_id, 
                text, 
                audience, 
                date 
            FROM 
                admin_announcement 
            WHERE 
                audience = 'Teachers' 
            ORDER BY 
                date DESC
        ");
        $stmt->execute();
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($announcements) > 0) {
            foreach ($announcements as $announcement) {
                echo '<div class="announcement">';
                echo '<h2>📢 Announcement</h2>';
                echo '<p>' . htmlspecialchars($announcement['text']) . '</p>';
                echo '<span class="date">Posted on: ' . htmlspecialchars($announcement['date']) . '</span>';
                echo '</div>';
            }
        } else {
            echo '<p>No announcements available for tutors at the moment.</p>';
        }
    } catch (PDOException $e) {
        die("Error fetching announcements: " . $e->getMessage());
    }
    ?>
    <button class="back-button" onclick="history.back()">Back</button>
</main>
</div>
</div>
</div>
<?php include '../footer.php'; ?>

</body>
</html>

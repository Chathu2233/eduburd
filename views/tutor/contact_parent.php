<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get the grade_class_id from the URL
if (!isset($_GET['grade_class_id'])) {
    die("Grade Class ID is required.");
}
$grade_class_id = $_GET['grade_class_id'];

// Fetch the student_id for the given grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT student_id 
        FROM grade_class 
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $grade_class = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$grade_class) {
        die("No student found for the given Grade Class ID.");
    }

    $student_id = $grade_class['student_id'];
} catch (PDOException $e) {
    die("Error fetching student ID: " . $e->getMessage());
}

// Fetch parent details for the given student_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name AS parent_first_name,
            u.last_name AS parent_last_name,
            u.email AS parent_email,
            u.contact_no AS parent_phone,
            u.profile_photo AS parent_photo, -- Fetch the profile photo
            u.first_name AS student_first_name,
            u.last_name AS student_last_name
        FROM 
            parent_student ps
        JOIN 
            parent p ON ps.parent_id = p.parent_id
        JOIN 
            user u ON p.user_id = u.user_id
        JOIN 
            student s ON ps.student_id = s.student_id
        WHERE 
            ps.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $parent_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parent_details) {
        die("No parent details found for the given student.");
    }
} catch (PDOException $e) {
    die("Error fetching parent details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Parent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/contact_parent.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <?php include 'sidebar1.php'; ?> <!-- Include the sidebar -->

        <div class="content-section">
            <!-- Section Title -->
            <div class="section-title">
                <h1>Parent Contact Details</h1>
            </div>

            <!-- Parent Details Card -->
            <section class="parent-details-card">
                <div class="parent-photo">
                    <img src="../../<?= htmlspecialchars($parent_details['parent_photo'] ?: 'assets/images/parent.jpg') ?>" alt="Parent Photo">
                </div>
                <div class="parent-info">
                    <h2><?= htmlspecialchars($parent_details['parent_first_name'] . ' ' . $parent_details['parent_last_name']) ?></h2>
                </div>
            </section>

            <!-- Contact Info Section -->
            <section class="contact-info">
                <div class="contact-item">
                    <h3>Email:</h3>
                    <p><?= htmlspecialchars($parent_details['parent_email']) ?></p>
                </div>
                <div class="contact-item">
                    <h3>Phone:</h3>
                    <p><?= htmlspecialchars($parent_details['parent_phone']) ?></p>
                </div>
            </section>

            <!-- Back Button -->
            <div class="back-button">
                <button class="styled-back-button" onclick="history.back()">← Back</button>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <?php include '../footer.php'; ?>
</body>
</html>

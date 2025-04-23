<?php
session_start();
require '../db.php'; // Include the database connection

// Check if student_id or grade_class_id is provided in the URL
if (!isset($_GET['student_id']) && !isset($_GET['grade_class_id'])) {
    die("Student ID or Grade Class ID not provided.");
}

if (isset($_GET['grade_class_id'])) {
    // Fetch student_id from grade_class table using grade_class_id
    $grade_class_id = $_GET['grade_class_id'];
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
} else {
    // Use student_id directly from the URL
    $student_id = $_GET['student_id'];
}

// Fetch student data from the user and student tables
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.dob, 
            u.contact_no, 
            u.profile_photo
        FROM 
            user u
        JOIN 
            student s ON u.user_id = s.user_id
        WHERE 
            s.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found.");
    }
} catch (PDOException $e) {
    die("Error fetching student data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/view_student.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>
    <!-- Student Profile Section -->
    <main class="profile-container">
        <section class="profile-card">
            <img src="../../<?= htmlspecialchars($student['profile_photo']) ?>" alt="Student Avatar" class="profile-avatar">
            <h2 class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h2>
            <p class="student-grade">Date of Birth: <?= htmlspecialchars($student['dob']) ?></p>
            <p class="student-email"><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p class="student-contact"><strong>Contact:</strong> <?= htmlspecialchars($student['contact_no']) ?></p>
            <div class="action-buttons">
                <a href="<?= htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'student_request.php') ?>" class="btn back-btn">Back</a>
            </div>
        </section>
    </main>
    <?php include '../footer.php'; ?>
</body>
</html>

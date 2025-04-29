<?php
// filepath: c:\xampp\htdocs\eduburd\views\tutor\student.php
session_start();
require '../db.php'; // Include the database connection

// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id'];

// Fetch student details for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name AS student_first_name,
            u.last_name AS student_last_name,
            u.email AS student_email,
            u.profile_photo AS student_photo,
            g.grade AS student_grade
        FROM 
            grade_class gc
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        JOIN 
            grade g ON gc.grade_id = g.grade_id
        WHERE 
            gc.grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="../../assets/css/tutor/view_student.css">
</head>
<body>
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <div class="container">
        <h1 class="page-title"> View Students</h1>
        <div class="students-container">
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                    <div class="student-card">
                        <img 
                            src="../../<?= htmlspecialchars($student['student_photo'] ?: 'assets/images/default_student.jpg') ?>" 
                            alt="Student Photo" 
                            class="student-photo"
                        >
                        <div class="student-details">
                            <h2><?= htmlspecialchars($student['student_first_name'] . ' ' . $student['student_last_name']) ?></h2>
                            <p><strong>Email:</strong> <?= htmlspecialchars($student['student_email']) ?></p>
                            <p><strong>Grade:</strong> <?= htmlspecialchars($student['student_grade']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-students-message">No students found for this class.</p>
            <?php endif; ?>
        </div>
        <div class="back-button">
            <button class="styled-back-button" onclick="history.back()">← Back</button>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
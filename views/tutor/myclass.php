<?php
session_start();
require_once '../db.php'; // Include your database connection file

// Check if the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    die("You must be logged in as a tutor to view this page.");
}

// Retrieve the tutor_id from the session
$tutor_id = $_SESSION['tutor_id'];

// Check if tutor_course_grade_id is passed in the URL
if (!isset($_GET['tutor_course_grade_id'])) {
    die("Grade ID not provided.");
}

$tutor_course_grade_id = $_GET['tutor_course_grade_id'];

// Fetch students for the selected course and grade
try {
    $stmt = $pdo->prepare("
        SELECT 
            gc.grade_class_id, 
            u.first_name, 
            u.last_name, 
            u.profile_photo,
            gc.description
        FROM 
            grade_class gc
        JOIN 
            tutor_course_grade tcg ON gc.grade_id = tcg.grade_id
        JOIN 
            tutor_course tc ON tcg.tutor_course_id = tc.tutor_course_id
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            tcg.tutor_course_grade_id = :tutor_course_grade_id
            AND gc.course_id = tc.course_id
            AND gc.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_course_grade_id', $tutor_course_grade_id, PDO::PARAM_INT);
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching classes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/myclass.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<section class="subjects">
    <h1>My Classes</h1>
    <div class="subjects-grid">
        <?php if (!empty($classes)): ?>
            <?php foreach ($classes as $class): ?>
                <a href="classschedule.php?grade_class_id=<?= htmlspecialchars($class['grade_class_id']) ?>" class="subject-card">
                <img src="../../<?= htmlspecialchars($class['profile_photo']?: 'assets/images/student2.jpg') ?>" alt="<?= htmlspecialchars($class['first_name']) ?>" class="subject-img">                    <p class="subject-name"><?= htmlspecialchars($class['first_name'] . ' ' . $class['last_name']) ?></p>
                    <p class="subject-description"><?= htmlspecialchars($class['description']) ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No classes available for this grade and course.</p>
        <?php endif; ?>
    </div>
</section>

<?php include '../footer.php'; ?>

</body>
</html>

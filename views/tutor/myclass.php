<?php
session_start();
require_once '../constants.php';
include '../db.php';

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    $_SESSION['error'] = "You must be logged in as a tutor to view classes.";
    header("Location: ../login.php");
    exit();
}

$tutor_id = $_SESSION['tutor_id'];

// Get the course_id from the query parameter
$course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : null;

if (!$course_id) {
    $_SESSION['error'] = "Invalid course ID.";
    header("Location: subject.php");
    exit();
}

// Fetch classes for the given course_id
try {
    $stmt = $pdo->prepare("SELECT grade_class_id, tutor_id, student_id, course_id, date, time, description FROM grade_class WHERE course_id = :course_id");
    $stmt->bindParam(':course_id', $course_id);
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
    <title>Online Tutoring Dashboard</title>
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
        <?php foreach ($classes as $class): ?>
            <a href="classschedule.php?grade_class_id=<?= htmlspecialchars($class['grade_class_id']) ?>" class="subject-card">
                <img src="../../assets/images/student.jpg" alt="Class Image" class="subject-img">
                <p class="subject-name">Class ID: <?= htmlspecialchars($class['grade_class_id']) ?></p>
                <p>Date: <?= htmlspecialchars($class['date']) ?></p>
                <p>Time: <?= htmlspecialchars($class['time']) ?></p>
                <p>Description: <?= htmlspecialchars($class['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../footer.php'; ?>

</body>
</html>

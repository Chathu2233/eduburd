<?php
session_start();
require_once '../constants.php';
include '../db.php';

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    $_SESSION['error'] = "You must be logged in as a tutor to view subjects.";
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch subjects for the logged-in tutor
try {
    $stmt = $pdo->prepare("SELECT course_id FROM tutor_subject WHERE tutor_id = :tutor_id");
    $stmt->bindParam(':tutor_id', $tutor_id);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch course names for the subjects
    $courseNames = [];
    foreach ($subjects as $subject) {
        $stmt = $pdo->prepare("SELECT name FROM course WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $subject['course_id']);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        $courseNames[$subject['course_id']] = $course['name'];
    }
} catch (PDOException $e) {
    die("Error fetching subjects: " . $e->getMessage());
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
    <link rel="stylesheet" href="../../assets/css/tutor/subject.css">
    <link rel="stylesheet" href="../../assets/css/tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<section class="subjects">
    <h1>My Subjects</h1>
    <a href="addsubject.php"><button class="add-subjects-btn">+Add Subjects</button></a>
    <div class="subjects-grid">
        <?php foreach ($subjects as $subject): ?>
            <div class="subject-card">
                <form action="deletesubject.php" method="POST" class="delete-form">
                    <input type="hidden" name="course_id" value="<?= htmlspecialchars($subject['course_id']) ?>">
                    <button type="button" class="delete-icon" onclick="confirmDelete(this)">🗑️</button>
                </form>
                <a href="grade.php?course_id=<?= htmlspecialchars($subject['course_id']) ?>">
                    <p><?= htmlspecialchars($courseNames[$subject['course_id']]) ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../footer.php'; ?>

<script>
function confirmDelete(button) {
    if (confirm("Are you sure you want to delete this subject?")) {
        button.closest('form').submit();
    }
}
</script>

</body>
</html>
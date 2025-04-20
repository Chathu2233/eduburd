<?php
session_start();
require_once '../constants.php';
include '../db.php';

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch subjects for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT tc.tutor_course_id, c.name AS subject_name
        FROM tutor_course tc
        JOIN course c ON tc.course_id = c.course_id
        WHERE tc.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../../assets/css/Tutor/subject.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css"> <!-- Add this line to include the sidebar styles -->
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="container">
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Centered images" width="50" height="50" style="margin-top: 30px;">
        <ul>
            <div class="sidebar1">
                <li><a href="my_account.php"><i class="fas fa-user"></i> My Profile</a></li>
            </div>
            <div class="sidebar2">
                <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i> My Subjects</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="student_request.php"><i class="fas fa-user-plus"></i> Student Requests</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="time_request.php"><i class="fas fa-user-plus"></i> Time slot Requests</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="announcement.php">Announcements</a></li>
            </div>
            <div class="sidebar5">
                <li><a href="../resourcelibrary.php">Resource Library</a></li>
            </div>
            <div class="sidebar6">
                <li><a href="editprofile.php">Edit Profile</a></li>
            </div>
        </ul>
    </div>

    <main class="dashboard">
        <section class="subjects">
            <h1>My Subjects</h1>
            <a href="addsubject.php"><button class="add-subjects-btn">+Add Subjects</button></a>
            <div class="subjects-grid">
                <?php foreach ($subjects as $subject): ?>
                    <a href="grade.php?tutor_course_id=<?= htmlspecialchars($subject['tutor_course_id']) ?>">
                        <div class="subject-card">
                            <span class="delete-icon">🗑️</span>
                            <p><?= htmlspecialchars($subject['subject_name']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>

<?php include '../footer.php'; ?>

</body>
</html>
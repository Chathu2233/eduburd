<?php
session_start();
require_once '../db.php'; // Include your database connection file

// Check if tutor_course_id is passed in the URL
if (!isset($_GET['tutor_course_id'])) {
    die("tutor_course_id not provided.");
}

$tutor_course_id = $_GET['tutor_course_id'];

// Handle grade deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tutor_course_grade_id'])) {
    $delete_tutor_course_grade_id = $_POST['delete_tutor_course_grade_id'];

    try {
        // Delete the grade from the database
        $stmt = $pdo->prepare("DELETE FROM tutor_course_grade WHERE tutor_course_grade_id = :tutor_course_grade_id");
        $stmt->bindParam(':tutor_course_grade_id', $delete_tutor_course_grade_id, PDO::PARAM_INT);
        $stmt->execute();

        // Set a success message
        $_SESSION['success_message'] = "Grade deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting grade: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: grade.php?tutor_course_id=" . htmlspecialchars($tutor_course_id));
    exit();
}

// Fetch grades for the selected tutor_course_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            tcg.tutor_course_grade_id, 
            g.grade, 
            tcg.qualification, 
            g.image
        FROM 
            tutor_course_grade tcg
        JOIN 
            grade g ON tcg.grade_id = g.grade_id
        WHERE 
            tcg.tutor_course_id = :tutor_course_id
    ");
    $stmt->bindParam(':tutor_course_id', $tutor_course_id, PDO::PARAM_INT);
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching grades: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/grade.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css">
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
                <li><a href="my_account.php"><i class="fas fa-user"></i>My Profile</a></li>
            </div>
            <div class="sidebar2">
                <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i>My Subjects</a></li>
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
            <h1>Grades</h1>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <a href="addgrade.php?tutor_course_id=<?= htmlspecialchars($tutor_course_id) ?>">
                <button class="add-subjects-btn">+Add Grade</button>
            </a>
            <div class="subjects-grid">
                <?php if (!empty($grades)): ?>
                    <?php foreach ($grades as $grade): ?>
                        <div class="subject-card" style="background-image: url('../../<?=htmlspecialchars($grade['image']) ?>');">
                            <!-- Delete Icon -->
                            <form action="grade.php?tutor_course_id=<?= htmlspecialchars($tutor_course_id) ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this grade?');">
                                <input type="hidden" name="delete_tutor_course_grade_id" value="<?= htmlspecialchars($grade['tutor_course_grade_id']) ?>">
                                <button type="submit" class="delete-icon">🗑️</button>
                            </form>

                            <!-- Grade Name -->
                            <a href="myclass.php?tutor_course_grade_id=<?= htmlspecialchars($grade['tutor_course_grade_id']) ?>" class="subject-name-link">
                                <p class="subject-name">Grade: <?= htmlspecialchars($grade['grade']) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No grades available for this course.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<!-- Footer Section -->
<?php include '../footer.php'; ?>

</body>
</html>
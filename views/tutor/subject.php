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

// Handle subject deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tutor_course_id'])) {
    $delete_tutor_course_id = $_POST['delete_tutor_course_id'];

    try {
        // Delete the subject from the database
        $stmt = $pdo->prepare("DELETE FROM tutor_course WHERE tutor_course_id = :tutor_course_id AND tutor_id = :tutor_id");
        $stmt->bindParam(':tutor_course_id', $delete_tutor_course_id, PDO::PARAM_INT);
        $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $stmt->execute();

        // Set a success message
        $_SESSION['success_message'] = "Subject deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting subject: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: subject.php");
    exit();
}

// Fetch subjects for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT tc.tutor_course_id, c.name AS subject_name, image AS subject_image
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
    <link rel="stylesheet" href="../../assets/css/Tutor/subject.css"><!-- Add this line to include the sidebar styles -->
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="container">
<?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

    <main class="content-section">
        <section class="subjects">
            <h1>My Subjects</h1>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <a href="addsubject.php"><button class="add-subjects-btn">+Add Subjects</button></a>
            <div class="subjects-grid">
                <?php foreach ($subjects as $subject): ?>
                    <div class="subject-card" style="background-image: url('../../<?= htmlspecialchars($subject['subject_image']) ?>');">
                        <!-- Delete Icon -->
                        <form action="subject.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                            <input type="hidden" name="delete_tutor_course_id" value="<?= htmlspecialchars($subject['tutor_course_id']) ?>">
                            <button type="submit" class="delete-icon">🗑️</button>
                        </form>

                        <!-- Subject Name as a Link -->
                        <a href="grade.php?tutor_course_id=<?= htmlspecialchars($subject['tutor_course_id']) ?>" class="subject-name-link">
                            <p class="subject-name"><?= htmlspecialchars($subject['subject_name']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>
                </div>

<?php include '../footer.php'; ?>

</body>
</html>
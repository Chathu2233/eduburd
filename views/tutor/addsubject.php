<?php 
session_start();
require_once '../constants.php';
include '../db.php';

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    header("Location: login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch courses from the course table
try {
    $stmt = $pdo->prepare("SELECT course_id, name FROM course");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = trim($_POST['course_id']); // Get selected course ID
    $qualifications = trim($_POST['qualifications']);

    // Validate input
    if (empty($course_id) || empty($qualifications)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: addsubject.php");
        exit();
    }

    try {
        // Insert into tutor_subject with tutor_id and course_id
        $stmt = $pdo->prepare("INSERT INTO tutor_subject (tutor_id, course_id, qualifications) VALUES (:tutor_id, :course_id, :qualifications)");
        $stmt->bindParam(':tutor_id', $tutor_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':qualifications', $qualifications);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject added successfully.";
        } else {
            $_SESSION['error'] = "Error adding subject.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: addsubject.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject - Online Tutoring Platform</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/addsubject.css">
</head>
<body>

    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <section class="add-subject-section">
        <h1>Add Subject</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-message'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<p class='success-message'>{$_SESSION['success']}</p>";
            unset($_SESSION['success']);
        }
        ?>

        <div class="form-container">
            <form action="addsubject.php" method="POST">
                
                <label for="course_id">Select Course</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Choose a Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course['course_id']) ?>">
                            <?= htmlspecialchars($course['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="qualifications">Qualifications</label>
                <input type="text" id="qualifications" name="qualifications" placeholder="Enter qualifications" required>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </section>

    <?php include '../footer.php'; ?>

</body>
</html>

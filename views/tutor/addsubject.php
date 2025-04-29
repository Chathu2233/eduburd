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
        // Check if the course already exists for the tutor
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count 
            FROM tutor_course 
            WHERE tutor_id = :tutor_id AND course_id = :course_id
        ");
        $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $_SESSION['error'] = "This course has already been added.";
            header("Location: addsubject.php");
            exit();
        }

        // Insert into tutor_course with tutor_id and course_id
        $stmt = $pdo->prepare("INSERT INTO tutor_course (tutor_id, course_id, qualifications) VALUES (:tutor_id, :course_id, :qualifications)");
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

        <!-- Display Success or Error Messages -->
        <?php
    if (isset($_SESSION['success'])) {
        echo "<div class='success-message'>{$_SESSION['success']}</div>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='error-message'>{$_SESSION['error']}</div>";
        unset($_SESSION['error']);
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

                <div class="form-controls">
                    <button type="submit" class="submit-btn">Submit</button>
                    <a href="subject.php" class="cancel-btn">Cancel</a> <!-- Added Cancel button -->
                </div>
            </form>
        </div>
    </section>

    <?php include '../footer.php'; ?>

</body>
</html>

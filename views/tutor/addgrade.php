<?php
session_start();
require_once '../db.php'; // Include your database connection file

// Ensure tutor_course_id is provided in the URL
if (!isset($_GET['tutor_course_id'])) {
    die("tutor_course_id not provided in the URL.");
}

$tutor_course_id = $_GET['tutor_course_id']; // Get the tutor_course_id from the URL

// Fetch grades available for the selected course
try {
    $stmt = $pdo->prepare("
        SELECT g.grade_id, g.grade
        FROM course_grade cg
        JOIN grade g ON cg.grade_id = g.grade_id
        WHERE cg.course_id = (
            SELECT course_id FROM tutor_course WHERE tutor_course_id = :tutor_course_id
        )
    ");
    $stmt->bindParam(':tutor_course_id', $tutor_course_id, PDO::PARAM_INT);
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching grades: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grade_id = trim($_POST['grade_id']); // Get the selected grade ID from the form
    $qualification = trim($_POST['qualification']); // Get the qualifications from the form

    // Validate input
    if (empty($grade_id) || empty($qualification)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: addgrade.php?tutor_course_id=$tutor_course_id");
        exit();
    }

    try {
        // Check if the grade already exists for the course
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM tutor_course_grade
            WHERE tutor_course_id = :tutor_course_id AND grade_id = :grade_id
        ");
        $stmt->bindParam(':tutor_course_id', $tutor_course_id, PDO::PARAM_INT);
        $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $_SESSION['error'] = "This grade has already been added to this course.";
            header("Location: addgrade.php?tutor_course_id=$tutor_course_id");
            exit();
        }

        // Insert grade and qualifications into tutor_course_grade table
        $stmt = $pdo->prepare("
            INSERT INTO tutor_course_grade (tutor_course_id, grade_id, qualification) 
            VALUES (:tutor_course_id, :grade_id, :qualification)
        ");
        $stmt->bindParam(':tutor_course_id', $tutor_course_id, PDO::PARAM_INT);
        $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);
        $stmt->bindParam(':qualification', $qualification, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Grade added successfully.";
        } else {
            $_SESSION['error'] = "Error adding grade.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: addgrade.php?tutor_course_id=$tutor_course_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grade</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/addgrade.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<section class="add-grade-section">
    <h1>Add Grade</h1>

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
        <form action="addgrade.php?tutor_course_id=<?= htmlspecialchars($tutor_course_id) ?>" method="POST">
            <label for="grade_id">Select Grade</label>
            <select id="grade_id" name="grade_id" required>
                <option value="">-- Choose a Grade --</option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?= htmlspecialchars($grade['grade_id']) ?>">
                        <?= htmlspecialchars($grade['grade']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="qualification">Qualifications</label>
            <input type="text" id="qualification" name="qualification" placeholder="Enter qualifications" required>

            <div class="form-controls">
                <button type="submit" class="submit-btn">Submit</button>
                <a href="grade.php?tutor_course_id=<?= htmlspecialchars($tutor_course_id) ?>" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</section>

<?php include '../footer.php'; ?>

</body>
</html>

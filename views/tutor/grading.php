<?php
session_start();
require '../db.php'; // Include the database connection

// Check if submission_id is provided in the URL
if (!isset($_GET['submission_id'])) {
    die("Submission ID not provided.");
}

$submission_id = $_GET['submission_id']; // Get submission_id from URL

// Fetch submission details for the given submission_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            assignment_submission_id AS submission_no, 
            file AS submission_file, 
            created_at AS submission_date, 
            assignment_id
        FROM 
            assignment_submission 
        WHERE 
            assignment_submission_id = :submission_id
    ");
    $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
    $stmt->execute();
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        die("Submission not found.");
    }

    $assignment_id = $submission['assignment_id']; // Get the assignment_id for redirection
} catch (PDOException $e) {
    die("Error fetching submission: " . $e->getMessage());
}

// Handle form submission for grading
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'];
    $marks = $_POST['marks'];
    $grade = $_POST['grade'];

    // Update the assignment_submission table with the grading details
    try {
        $stmt = $pdo->prepare("
            UPDATE assignment_submission 
            SET 
                comment = :comment, 
                marks = :marks, 
                grade = :grade 
            WHERE 
                assignment_submission_id = :submission_id
        ");
        $stmt->execute([
            ':comment' => $comment,
            ':marks' => $marks,
            ':grade' => $grade,
            ':submission_id' => $submission_id,
        ]);

        // Set a success message
        $_SESSION['success_message'] = "Submission graded successfully!";
        // Redirect to the assignment submissions page
        header("Location: view_submission.php?assignment_id=" . htmlspecialchars($assignment_id));
        exit();
    } catch (PDOException $e) {
        die("Error updating submission: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Submission</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/grading.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
</head>
<body>

<header>
   <?php include '../header_tutor.php'; ?>
</header>

<!-- Grading Form Section -->
<main class="form-container">
    <h1>Grade Student Submission</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form action="grading.php?submission_id=<?= htmlspecialchars($submission_id) ?>" method="POST" class="grading-form">
        <label for="submission-no">Submission No</label>
        <input type="text" id="submission-no" name="submission-no" value="<?= htmlspecialchars($submission['submission_no']) ?>" readonly>

        <label for="submission-title">Submission File</label>
        <a href="<?= htmlspecialchars($submission['submission_file']) ?>" download>
            <button type="button" class="view-btn">Download File</button>
        </a>

        <label for="comment">Comment</label>
        <textarea id="comment" name="comment" placeholder="Enter your comment" required></textarea>

        <label for="marks">Marks</label>
        <input type="number" id="marks" name="marks" placeholder="Enter marks" required>

        <label for="grade">Grade</label>
        <input type="text" id="grade" name="grade" placeholder="Enter grade" required>

        <div class="form-controls">
            <button type="button" class="cancel-button" onclick="history.back()">Cancel</button>
            <button type="submit" class="add-button">Submit Grade</button>
        </div>
    </form>
</main>

<!-- Footer Section -->
<?php include '../footer.php'; ?>
</body>
</html>
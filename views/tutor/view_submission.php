<?php
session_start();
require '../db.php'; // Include the database connection

// Check if assignment_id is provided in the URL
if (!isset($_GET['assignment_id'])) {
    die("Assignment ID not provided.");
}

$assignment_id = $_GET['assignment_id']; // Get assignment_id from URL

// Handle file download
if (isset($_GET['download_submission_id'])) {
    $submission_id = $_GET['download_submission_id'];

    try {
        // Fetch the file content and metadata from the database
        $stmt = $pdo->prepare("
            SELECT 
                asub.file AS submission_file, 
                asub.assignment_submission_id, 
                u.first_name, 
                u.last_name 
            FROM 
                assignment_submission asub
            JOIN 
                assignment a ON asub.assignment_id = a.assignment_id
            JOIN 
                grade_class gc ON a.grade_class_id = gc.grade_class_id
            JOIN 
                student s ON gc.student_id = s.student_id
            JOIN 
                user u ON s.user_id = u.user_id
            WHERE 
                asub.assignment_submission_id = :submission_id
        ");
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->execute();
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$submission) {
            die("File not found.");
        }

        $file_content = $submission['submission_file'];

        // Generate a default file name using the student's name and submission ID
        $file_name = "Submission_" . htmlspecialchars($submission['first_name'] . "_" . $submission['last_name'] . "_" . $submission['assignment_submission_id']) . ".pdf";

        // Serve the file as a download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file_name);
        header("Content-Length: " . strlen($file_content));
        echo $file_content;
        exit();
    } catch (PDOException $e) {
        die("Error fetching file: " . $e->getMessage());
    }
}

// Fetch submissions for the given assignment_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            asub.assignment_submission_id, 
            asub.created_at AS submission_date, 
            asub.marks, 
            asub.grade,
            asub.file,
            a.title AS assignment_title, 
            a.description AS assignment_description, 
            a.deadline AS assignment_deadline, 
            u.first_name, 
            u.last_name
        FROM 
            assignment_submission asub
        JOIN 
            assignment a ON asub.assignment_id = a.assignment_id
        JOIN 
            grade_class gc ON a.grade_class_id = gc.grade_class_id
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            asub.assignment_id = :assignment_id
    ");
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching submissions: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/view_submission.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>


    <!-- Main Content -->
    <main>
        <!-- Submissions Section -->
        <section class="assignments-section">
            <h2 class="section-title">Submissions</h2>
            <table class="assignments-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Submission Date</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Grading</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']) ?></td>
                                <td><?= htmlspecialchars($submission['submission_date']) ?></td>
                                <td>
                                    <a href="view_submission.php?assignment_id=<?= htmlspecialchars($assignment_id) ?>&download_submission_id=<?= htmlspecialchars($submission['assignment_submission_id']) ?>">
                                        <button class="view-btn">📥 Download</button>
                                    </a>
                                </td>
                                <td>
                                    <?= ($submission['marks'] !== null && $submission['marks'] > 0) 
                                        ? '<span class="graded">Graded</span>' 
                                        : '<span class="not-graded">Not Graded</span>' ?>
                                </td>
                                <td>
                                    <?php if ($submission['marks'] !== null && $submission['marks'] > 0): ?>
                                        <button class="graded-btn" disabled>Graded: <?= htmlspecialchars($submission['grade']) ?></button>
                                    <?php else: ?>
                                        <form action="grading.php" method="GET">
                                            <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission['assignment_submission_id']) ?>">
                                            <button type="submit" class="grade-btn">Grade</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-submissions-message">No submissions available for this assignment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Back Button -->
        <div class="back-button">
            <button class="styled-back-button" onclick="history.back()">← Back</button>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

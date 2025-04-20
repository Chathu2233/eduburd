<?php
session_start();
require '../db.php'; // Include the database connection

// Check if assignment_id is provided in the URL
if (!isset($_GET['assignment_id'])) {
    die("Assignment ID not provided.");
}

$assignment_id = $_GET['assignment_id']; // Get assignment_id from URL

// Fetch submissions for the given assignment_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            asub.assignment_submission_id, 
            asub.file AS submission_file, 
            asub.created_at AS submission_date, 
            asub.marks, 
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

    <!-- Page Title -->
    <h1 class="dashboard-title">Assignment Submissions</h1>

    <!-- Main Content -->
    <main>
        <!-- Submissions Section -->
        <section class="assignments-section">
            <h2>Submissions for Assignment</h2>
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
                                    <a href="download.php?file=<?= urlencode($submission['submission_file']) ?>">
                                        <button class="view-btn">Download</button>
                                    </a>
                                </td>
                                <td>
                                    <?= ($submission['marks'] !== null && $submission['marks'] > 0) 
                                        ? '<span class="graded">Graded</span>' 
                                        : '<span class="not-graded">Not Graded</span>' ?>
                                </td>
                                <td>
                                    <form action="grading.php" method="GET">
                                        <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission['assignment_submission_id']) ?>">
                                        <button type="submit" class="view-btn" <?= ($submission['marks'] !== null && $submission['marks'] > 0) ? 'disabled' : '' ?>>Grade</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No submissions available for this assignment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <button class="back-button" onclick="history.back()">Back</button>
    </main>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

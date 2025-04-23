<?php
session_start();
require_once '../constants.php';
require_once '../db.php';

// Get query parameters
$student_id = $_GET['student_id'] ?? null;
$course_id = $_GET['course_id'] ?? null;
$tutor_id = $_GET['tutor_id'] ?? null;

// Fetch assignments and their submission status
$assignments = [];
if ($student_id && $course_id && $tutor_id) {
    $stmt = $pdo->prepare("
        SELECT 
            a.assignment_id, 
            a.title, 
            a.description, 
            a.deadline, 
            s.file AS submission_file
        FROM 
            assignment a
        LEFT JOIN 
            assignment_submission s 
        ON 
            a.assignment_id = s.assignment_id
        INNER JOIN 
            grade_class gc 
        ON 
            a.grade_class_id = gc.grade_class_id
        WHERE 
            gc.student_id = :student_id
            AND gc.course_id = :course_id
            AND gc.tutor_id = :tutor_id
    ");
    $stmt->execute([
        ':student_id' => $student_id,
        ':course_id' => $course_id,
        ':tutor_id' => $tutor_id,
    ]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Assignment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/pendingassignments.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div >
                <h2>Pending Assignment</h2>
                <!-- Pending Homework Table -->
                <table class="homework-table">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($assignments)): ?>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                    <td class="<?php echo $assignment['submission_file'] ? 'status-done' : 'status-pending'; ?>">
                                        <?php echo $assignment['submission_file'] ? 'Done' : 'Pending'; ?>
                                    </td>
                                    <td>
                                        <?php if ($assignment['submission_file']): ?>
                                            <a href="download_submission.php?assignment_id=<?php echo $assignment['assignment_id']; ?>" target="_blank" class="btn-view">View</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No assignments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
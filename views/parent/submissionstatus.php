<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include the database connection


// Handle file download request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_path'])) {
    $file_path = $_POST['file_path'];
    $full_path = ROOT . '/assets/views/uploads/' . $file_path;

    if (file_exists($full_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($full_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($full_path));
        readfile($full_path);
        exit;
    } else {
        die('File not found.');
    }
}
// Get the student_id from the query parameter
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die("Student ID is required.");
}
$student_id = $_GET['student_id'];

// Fetch the student's full name
try {
    $stmt = $pdo->prepare("
        SELECT CONCAT(u.first_name, ' ', u.last_name) AS full_name
        FROM student s
        INNER JOIN user u ON s.user_id = u.user_id
        WHERE s.student_id = :student_id
    ");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found.");
    }
    $student_name = htmlspecialchars($student['full_name']);
} catch (PDOException $e) {
    die("Error fetching student name: " . $e->getMessage());
}

// Fetch assignment submission details from the database
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.title AS assignment_name,
            c.name AS subject, -- Fetch course name instead of ID
            a.description AS topic,
            a.deadline AS submission_date,
            asub.grade AS grade,
            asub.comment AS feedback,
            asub.file AS submission_file,
            CASE 
                WHEN asub.created_at IS NULL THEN 'Pending'
                WHEN asub.created_at > a.deadline THEN 'Overdue'
                ELSE 'Submitted'
            END AS status
        FROM 
            assignment_submission asub
        RIGHT JOIN 
            assignment a ON asub.assignment_id = a.assignment_id
        INNER JOIN 
            grade_class g ON a.grade_class_id = g.grade_class_id
        INNER JOIN 
            course c ON g.course_id = c.course_id -- Join with course table to get course name
        WHERE 
            g.student_id = :student_id
        ORDER BY 
            a.deadline DESC
    ");
    $stmt->execute(['student_id' => $student_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - Assignment Submission Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/submissionstatus.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <section class="center-text" >
                <h2><?php echo $student_name; ?>'s assignment submission status</h2>
                <p>Track <?php echo $student_name; ?>'s assignments and their current status here.</p>
            </section>

            <!-- Assignments Table -->
            <section class="faq-section">
                <h2>Assignment details</h2>
                <div class="faq-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Assignment Name</th>
                                <th>Course</th>
                                <th>Topic</th>
                                <th>Grade</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assignments)): ?>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['assignment_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['topic']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['grade'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['feedback'] ?? ''); ?></td>
                                        <td>
                                            <?php if (!empty($assignment['submission_file'])): ?>
                                                <form method="post" action="">
                                                    <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($assignment['submission_file']); ?>">
                                                    <button type="submit" class="download-btn">Download</button>
                                                </form>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No assignments found for this student.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
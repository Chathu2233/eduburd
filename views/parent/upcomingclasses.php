<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include the database connection

// Get the parameters from the URL
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$tutor_id = isset($_GET['tutor_id']) ? intval($_GET['tutor_id']) : 0;

// Fetch upcoming classes from the database
$classes = [];
if ($student_id && $course_id && $tutor_id) {
    $query = "
        SELECT 
            gc.day AS date,
            gc.time,
            c.name AS topic
        FROM 
            grade_class gc
        INNER JOIN 
            course c ON gc.course_id = c.course_id
        WHERE 
            gc.student_id = :student_id
            AND gc.course_id = :course_id
            AND gc.tutor_id = :tutor_id
        ORDER BY 
            gc.day, gc.time
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':student_id' => $student_id,
        ':course_id' => $course_id,
        ':tutor_id' => $tutor_id
    ]);

    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Classes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/upcomingclasses.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
    <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div >
                <h2>Upcoming Classes</h2>
                <!-- Upcoming Classes Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['date']); ?></td>
                                    <td><?php echo htmlspecialchars($class['time']); ?></td>
                                    <td><?php echo htmlspecialchars($class['topic']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No upcoming classes found.</td>
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
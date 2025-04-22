<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include the database connection

// Assuming the student_id is stored in the session
$student_id = $_SESSION['student_id'] ?? null;

$upcoming_classes = [];

if ($student_id) {
    try {
        // Query to fetch upcoming classes for the specific student, including course name
        $stmt = $pdo->prepare("
            SELECT gc.day, gc.time, c.name AS course_name, gc.description 
            FROM grade_class gc
            JOIN course c ON gc.course_id = c.course_id
            WHERE gc.student_id = :student_id
            ORDER BY FIELD(gc.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), gc.time
        ");
        $stmt->execute(['student_id' => $student_id]);
        $upcoming_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debugging: Check if data is fetched
        if (empty($upcoming_classes)) {
            error_log("No upcoming classes found for student_id: $student_id");
        }
    } catch (PDOException $e) {
        error_log("Error fetching upcoming classes: " . $e->getMessage());
        die("Error fetching upcoming classes. Please try again later.");
    }
} else {
    error_log("Student ID is not set in the session.");
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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/classschedule.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
<header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>
        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h1 class="page-title">Upcoming Classes</h1>
                <div class="table-wrapper">
                    <table class="class-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Course</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($upcoming_classes)): ?>
                                <?php foreach ($upcoming_classes as $class): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($class['day']); ?></td>
                                        <td><?php echo htmlspecialchars(date("h:i A", strtotime($class['time']))); ?></td>
                                        <td><?php echo htmlspecialchars($class['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($class['description']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No upcoming classes found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

   <!-- Footer -->
   <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
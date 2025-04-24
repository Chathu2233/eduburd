<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include the database connection

// Fetch finished classes for the specific student, course, and tutor
$student_id = $_SESSION['student_id']; // Assuming student_id is stored in session
$course_id = $_GET['course_id']; // Assuming course_id is passed as a query parameter
$tutor_id = $_GET['tutor_id']; // Assuming tutor_id is passed as a query parameter

try {
    $stmt = $pdo->prepare("
        SELECT date, time, day, description 
        FROM grade_class 
        WHERE student_id = :student_id 
          AND course_id = :course_id 
          AND tutor_id = :tutor_id
    ");
    $stmt->execute([
        ':student_id' => $student_id,
        ':course_id' => $course_id,
        ':tutor_id' => $tutor_id
    ]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter out past dates
    $currentDate = date('Y-m-d');
    $classes = array_filter($classes, function ($class) use ($currentDate) {
        return $class['date'] > $currentDate;
    });
} catch (PDOException $e) {
    die("Error fetching class history: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/classhistory.css">
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
                <h2>Upcoming classes </h2>
                <!-- Class History Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Day</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="class-history-table-body">
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['date']); ?></td>
                                    <td><?php echo htmlspecialchars($class['time']); ?></td>
                                    <td><?php echo htmlspecialchars($class['day']); ?></td>
                                    <td><?php echo htmlspecialchars($class['description']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No upcoming classes found.</td>
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
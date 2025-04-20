<?php
session_start();
require '../db.php'; // Include your DB connection

// Check if student_id is passed
if (!isset($_GET['student_id'])) {
    die("Missing student_id.");
}

$student_id = $_GET['student_id'];

try {
    // Fetch tutors and their status
    $stmt = $pdo->prepare("
    SELECT 
        u.first_name AS tutor_name,
        t.tutor_id,
        tsr.time_slot_id,
        tsr.course_id,
        tsr.grade_id,
        tsr.status,
        ts.start_time,
        ts.end_time,
        ts.day,
        gc.grade_class_id
    FROM time_slot_request tsr
    JOIN time_slot ts ON tsr.time_slot_id = ts.time_slot_id
    JOIN tutor t ON ts.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    LEFT JOIN grade_class gc ON gc.tutor_id = t.tutor_id AND gc.student_id = tsr.student_id
    WHERE tsr.student_id = :student_id
    ");
    $stmt->execute([
        ':student_id' => $student_id
    ]);

    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check for accepted requests and insert into grade_class
    foreach ($tutors as $tutor) {
        if ($tutor['status'] === 'accepted' && empty($tutor['grade_class_id'])) {
            // Check if a grade_class already exists for this tutor and student
            $checkStmt = $pdo->prepare("
                SELECT grade_class_id 
                FROM grade_class 
                WHERE tutor_id = :tutor_id AND student_id = :student_id
            ");
            $checkStmt->execute([
                ':tutor_id' => $tutor['tutor_id'],
                ':student_id' => $student_id
            ]);
            $existingGradeClass = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingGradeClass) {
                // Combine start_time and end_time into a single time range
                $time = $tutor['start_time'] . ' - ' . $tutor['end_time'];

                // Insert into grade_class table
                $insertStmt = $pdo->prepare("
                    INSERT INTO grade_class (tutor_id, student_id, grade_id, course_id, day, time, description)
                    VALUES (:tutor_id, :student_id, :grade_id, :course_id, :day, :time, :description)
                ");
                $insertStmt->execute([
                    ':tutor_id' => $tutor['tutor_id'],
                    ':student_id' => $student_id,
                    ':grade_id' => $tutor['grade_id'],
                    ':course_id' => $tutor['course_id'],
                    ':day' => $tutor['day'],
                    ':time' => $time,
                    ':description' => 'Class scheduled by the system.' // Default description
                ]);

                // Fetch the newly inserted grade_class_id
                $tutor['grade_class_id'] = $pdo->lastInsertId();
            } else {
                // Use the existing grade_class_id
                $tutor['grade_class_id'] = $existingGradeClass['grade_class_id'];
            }
        }
    }
} catch (PDOException $e) {
    die("Error fetching tutors: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Tutors</title>
    <link rel="stylesheet" href="../../assets/css/student/tutor.css">
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include 'sidebar.php'; ?>
        </aside>

        <!-- Main Content -->
        <div class="content">
            <h1>Course Tutors</h1>

            <?php if (empty($tutors)): ?>
                <p>No tutors found for this student.</p>
            <?php else: ?>
                <div class="courses">
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="course">
                            <?php if ($tutor['status'] === 'accepted' && !empty($tutor['grade_class_id'])): ?>
                                <a href="class.php?grade_class_id=<?php echo urlencode($tutor['grade_class_id']); ?>" class="tutor-link">
                            <?php elseif ($tutor['status'] === 'pending'): ?>
                                <a href="classschedule.php?tutor_id=<?php echo urlencode($tutor['tutor_id']); ?>&student_id=<?php echo urlencode($student_id); ?>" class="tutor-link">
                            <?php else: ?>
                                <a href="#" class="tutor-link" style="pointer-events: none; color: gray;"> <!-- Disabled link -->
                            <?php endif; ?>
                                <h3><?php echo htmlspecialchars($tutor['tutor_name']); ?></h3>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="stu_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

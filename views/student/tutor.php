<?php
session_start();
require '../db.php'; // Include your DB connection

// Check if course_id and student_id are passed
if (!isset($_GET['course_id']) || !isset($_GET['student_id'])) {
    die("Missing course_id or student_id.");
}

$course_id = $_GET['course_id'];
$student_id = $_GET['student_id'];

try {
    // Fetch all tutors and grade_class_id for the given course_id and student_id
    $stmt = $pdo->prepare("
        SELECT 
            DISTINCT t.tutor_id,
            CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
            t.years_of_experience,
            t.description,
            gc.grade_class_id
        FROM grade_class gc
        JOIN tutor t ON gc.tutor_id = t.tutor_id
        JOIN user u ON t.user_id = u.user_id
        WHERE gc.course_id = :course_id AND gc.student_id = :student_id
    ");
    $stmt->execute([':course_id' => $course_id, ':student_id' => $student_id]);
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tutors)) {
        die("No tutors found for the given course and student.");
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
    <title>Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/tutor.css">
</head>
<body>
    
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Parent Content -->
        <main class="dashboard">
<section>

            <h1>Tutor Details</h1>

            <div class="tutor-details">
    <?php foreach ($tutors as $tutor): ?>
        <div class="tutor-card">
            <h2><?php echo htmlspecialchars($tutor['tutor_name']); ?></h2>
            <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>
            <!-- Button to redirect to class.php with grade_class_id -->
            <a href="class.php?grade_class_id=<?php echo htmlspecialchars($tutor['grade_class_id']); ?>" class="btn">
                View Class Schedule
            </a>
        </div>
    <?php endforeach; ?>
</div>
        </div>
    </div>
               
</section>
        </main>
        </div>

     <!-- Footer -->
     <?php include '../footer.php'; ?>

     </body>
</html>
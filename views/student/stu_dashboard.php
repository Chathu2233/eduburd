<?php 
session_start();
require '../db.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

try {
    // STEP 1: Get student_id for logged-in user
    $stmt1 = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $stmt1->execute([':user_id' => $user_id]);
    $student = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student record not found for user_id: $user_id");
    }

    $student_id = $student['student_id'];

    // STEP 2: Get unique courses for the student
    $stmt2 = $pdo->prepare("
        SELECT DISTINCT 
            gc.course_id,
            c.name AS course_name
        FROM 
            grade_class gc
        JOIN 
            course c ON gc.course_id = c.course_id
        WHERE 
            gc.student_id = :student_id
    ");
    $stmt2->execute([':student_id' => $student_id]);
    $courses = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/student/stu_dashboard.css"> 
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Dashboard Content -->
        <main class="dashboard">
            <section class="welcome">
                <h1>WELCOME BACK, STUDENT!</h1>
                <p>Always Stay Updated In Your Student Portal</p>
            </section>

            <!-- Enrolled Courses Section -->
            <section class="enrolled-courses">
                <h2>Enrolled Courses</h2>
                <div class="courses">
                    <?php if (empty($courses)): ?>
                        <p>You are not enrolled in any courses yet.</p>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <div class="course">
                                <h3>
                                    <a href="tutor.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>&student_id=<?php echo htmlspecialchars($student_id); ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </a>
                                </h3>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

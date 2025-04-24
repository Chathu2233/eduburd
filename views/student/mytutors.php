<?php 
session_start();
require '../db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // This should be the student's user_id

try {
    // Get the student's student_id using their user_id
    $stmtStudent = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $stmtStudent->execute([':user_id' => $user_id]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found.");
    }

    $student_id = $student['student_id'];

    // Get tutor name, status, and date from tutor_student_request table
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name AS tutor_first_name, 
            u.last_name AS tutor_last_name, 
            tsr.status, 
            tsr.date, 
            t.tutor_id
        FROM tutor_student_request tsr
        INNER JOIN tutor t ON tsr.tutor_id = t.tutor_id
        INNER JOIN user u ON t.user_id = u.user_id
        WHERE tsr.student_id = :student_id
        ORDER BY tsr.date DESC
    ");
    $stmt->execute([':student_id' => $student_id]);
    $tutor_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tutor Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/mytutors.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <!-- Content -->
        <main class="dashboard">
            <section class="tutor-section">
                <h1>My Tutor Requests</h1>
                <table class="tutor-table">
                    <thead>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tutor_requests)): ?>
                            <tr>
                                <td colspan="3">No tutor requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutor_requests as $request): ?>
                                <tr>
                                    <td>
                                        <?php if (strtolower($request['status']) === 'accepted'): ?>
                                            <a href="classschedule.php?tutor_id=<?php echo htmlspecialchars($request['tutor_id']); ?>">
                                                <?php echo htmlspecialchars($request['tutor_first_name'] . ' ' . $request['tutor_last_name']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($request['tutor_first_name'] . ' ' . $request['tutor_last_name']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($request['status'])); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($request['date']))); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>

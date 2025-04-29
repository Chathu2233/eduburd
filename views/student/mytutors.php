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

    // Handle delete request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request_id'])) {
        $deleteRequestId = $_POST['delete_request_id'];

        $stmtDelete = $pdo->prepare("DELETE FROM tutor_student_request WHERE tutor_student_request_id = :request_id AND student_id = :student_id AND status = 'pending'");
        $stmtDelete->execute([':request_id' => $deleteRequestId, ':student_id' => $student_id]);

        // Redirect to avoid form resubmission
        header("Location: mytutors.php");
        exit();
    }

    // Get tutor name, status, date from tutor_student_request table
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name AS tutor_first_name, 
            u.last_name AS tutor_last_name, 
            tsr.status, 
            tsr.date, 
            tsr.tutor_student_request_id, 
            t.tutor_id,
            (SELECT tsr2.status 
             FROM time_slot_request tsr2
             INNER JOIN time_slot ts ON tsr2.time_slot_id = ts.time_slot_id
             WHERE ts.tutor_id = t.tutor_id 
               AND tsr2.student_id = :student_id 
             ORDER BY tsr2.time_slot_request_id DESC 
             LIMIT 1
            ) AS time_slot_status
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
          

                <h1>My tutor requests</h1>
                <table class="tutor-table">
                    <thead>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Time Slot Status</th> <!-- Added new column -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tutor_requests)): ?>
                            <tr>
                                <td colspan="5">No tutor requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutor_requests as $request): ?>
                                <tr>
                                    <td>
                                        <?php if (strtolower($request['status']) === 'accepted'): ?>
                                            <a href="classschedule.php?tutor_id=<?php echo htmlspecialchars($request['tutor_id']); ?>&student_id=<?php echo htmlspecialchars($student_id); ?>">
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
                                    <td>
                                        <?php 
                                        if (!empty($request['time_slot_status'])) {
                                            echo htmlspecialchars(ucfirst($request['time_slot_status']));
                                        } else {
                                            echo 'No Request';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (strtolower($request['status']) === 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="delete_request_id" value="<?php echo htmlspecialchars($request['tutor_student_request_id']); ?>">
                                                <button type="submit" class="delete-button">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
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

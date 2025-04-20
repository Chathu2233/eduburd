<!-- filepath: c:\xampp\htdocs\eduburd\views\student\mytutors.php -->
<?php 
session_start();
require '../db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Debugging: Check the logged-in student ID
echo "Logged-in User ID: " . $_SESSION['user_id'] . "<br>";

// Fetch tutor requests for the logged-in student
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("
        SELECT 
            tsr.tutor_student_request_id, 
            t.tutor_id, 
            u.first_name AS tutor_first_name, 
            u.last_name AS tutor_last_name, 
            tsr.status, 
            tsr.date
        FROM tutor_student_request tsr
        INNER JOIN tutor t ON tsr.tutor_id = t.tutor_id
        INNER JOIN user u ON t.user_id = u.user_id
        WHERE tsr.student_id = :student_id
        ORDER BY tsr.date DESC
    ");
    $stmt->execute([':student_id' => $user_id]);
    $tutor_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Output the query and results
    echo "Executed Query: ";
    echo $stmt->queryString;
    echo "<br>Bound Parameters: ";
    print_r([':student_id' => $user_id]);
    echo "<br>Results: ";
    print_r($tutor_requests);
    exit;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tutors</title>
    <link rel="stylesheet" href="../../assets/css/student/mytutors.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
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

        <!-- Tutor Requests Content -->
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
                                        <?php echo htmlspecialchars($request['tutor_first_name'] . ' ' . $request['tutor_last_name']); ?>
                                    </td>
                                    <td class="status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($request['status'])); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($request['date']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
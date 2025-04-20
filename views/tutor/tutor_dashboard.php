<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}

$tutor_id = $_SESSION['tutor_id'];

// Fetch student requests for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT 
            tsr.tutor_student_request_id, 
            tsr.student_id, 
            tsr.status, 
            u.first_name, 
            u.last_name 
        FROM 
            tutor_student_request tsr
        JOIN 
            student s ON tsr.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            tsr.tutor_id = :tutor_id
        ORDER BY 
            tsr.tutor_student_request_id DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $student_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student requests: " . $e->getMessage());
}

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['accept', 'reject'])) {
        die("Invalid action.");
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE tutor_student_request 
            SET status = :status 
            WHERE tutor_student_request_id = :request_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':status' => $action === 'accept' ? 'accepted' : 'rejected',
            ':request_id' => $request_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Redirect to avoid form resubmission
        header("Location: student_requests.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating request status: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css">
</head>
<body>
<header >
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <div class="container">
        
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Centered images"  width="50" height="50" style="margin-top: 30px; "  style="background-color: pink;">
        <ul>
        <div class="sidebar1">

            <li><a href="my_account.php"><i class="fas fa-user"></i>My Profile</a></li>
        </div>

        <div class="sidebar2">
            <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i>My Subjects</a></li>
        </div>

        <div class="sidebar3">

            <li><a href="student_request.php"><i class="fas fa-user-plus"></i> Student Requests</a></li>
        </div>

        <div class="sidebar3">
        <li><a href="announcement.php">Announcements</a></li>
        </div>
        <div class="sidebar5">
        <li><a href="../resourcelibrary.php">Resource Library</a></li>
        </div>

        <div class="sidebar6">
        <li><a href="editprofile.php">Edit Profile</a></li>
        </div>


        </ul>
    </div>
        <main class="dashboard">
            <section class="welcome">
                <h2>Welcome Back, Tutor!</h2>
                <p>Provide the best support to students.</p>
            </section>
            <section class="upcoming-classes">
                <h3>Upcoming Classes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Math Class</td>
                            <td>25th Nov</td>
                            <td>10:00 AM</td>
                            <td><button class="btn">Join Now</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <section class="student-requests">
                <h3>Student Requests</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                            <td><?= htmlspecialchars($request['status']) ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['tutor_student_request_id']) ?>">
                                    <button type="submit" name="action" value="accept" class="btn accept">Accept</button>
                                    <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="class-requests">
            <h2>Class Requests</h2>
            <table>
                <tr>
                    <th>Time Slot</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Action</th>
                    <th></th>
                </tr>
                <tr>
                    <td>Time slot 1</td>
                    <td>Name</td>
                    <td>Grade</td>
                    <td><button class="btn accept">Accept</button> 
                    <button class="btn reject">Reject</button></td>
                </tr>
                
            </table>
        </section>

        <section class="view submissions">
            <h2>Recent Submissions</h2>
            <table>
                <tr>
                    <th>Student</th>
                    <th>Assignment No</th>
                    <th>Submissions</th>
                    <th>Grading</th>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>Assignment No</td>
                    <td><button class="btn accept"><a href = "view_submission.php">View</button> </td>
                    <td><button class="btn accept"><a href = "grading.php">Grade</button> </td>
                </tr>
                
            </table>
        </section>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>

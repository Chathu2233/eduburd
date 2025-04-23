<?php
session_start();
require '../db.php'; // Include the database connection
date_default_timezone_set('Asia/Colombo'); // Replace with your time zone

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
            tsr.date,
            u.first_name, 
            u.last_name 
        FROM 
            tutor_student_request tsr
        JOIN 
            student s ON tsr.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            tsr.tutor_id = :tutor_id AND tsr.status = 'pending'
        ORDER BY 
            tsr.tutor_student_request_id DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $student_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student requests: " . $e->getMessage());
}

// Fetch time slot requests for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT 
            tsr.time_slot_request_id, 
            tsr.time_slot_id, 
            tsr.student_id, 
            tsr.grade_id, 
            tsr.course_id, 
            tsr.status, 
            g.grade, 
            c.name, 
            u.first_name, 
            u.last_name 
        FROM 
            time_slot_request tsr
        JOIN 
            student s ON tsr.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        JOIN 
            grade g ON tsr.grade_id = g.grade_id
        JOIN 
            course c ON tsr.course_id = c.course_id
        WHERE 
            tsr.status = 'pending'
        ORDER BY 
            tsr.time_slot_request_id DESC
    ");
    $stmt->execute();
    $time_slot_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching time slot requests: " . $e->getMessage());
}

// Fetch recent submissions for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT 
            asub.assignment_submission_id, 
            asub.file AS submission_file, 
            asub.created_at AS submission_date, 
            asub.grade AS submission_grade, 
            asub.comment AS submission_comment, 
            asub.marks AS submission_marks, 
            a.assignment_id, 
            a.title AS assignment_title, 
            a.deadline AS assignment_deadline, 
            gc.grade_class_id, 
            u.first_name AS student_first_name, 
            u.last_name AS student_last_name
        FROM 
            assignment_submission asub
        JOIN 
            assignment a ON asub.assignment_id = a.assignment_id
        JOIN 
            grade_class gc ON a.grade_class_id = gc.grade_class_id
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            gc.tutor_id = :tutor_id
        ORDER BY 
            asub.created_at DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $recent_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching recent submissions: " . $e->getMessage());
}

// Fetch classes for today based on the tutor_id and current day
try {
    // Get today's date and day name
    $today = new DateTime();
    $current_day_name = $today->format('l'); // e.g., "Monday"
    $current_date = $today->format('Y-m-d'); // e.g., "2025-04-23"

    // Fetch classes for the specific tutor_id and today's day
    $stmt = $pdo->prepare("
        SELECT 
            gc.grade_class_id, 
            g.grade AS grade_name, 
            c.name AS course_name, 
            gc.day, 
            gc.time,
            t.link AS zoom_link
        FROM 
            grade_class gc
        JOIN 
            grade g ON gc.grade_id = g.grade_id
        JOIN 
            course c ON gc.course_id = c.course_id
        JOIN 
            tutor t ON gc.tutor_id = t.tutor_id
        WHERE 
            gc.tutor_id = :tutor_id
            AND gc.day = :current_day_name
        ORDER BY gc.time ASC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_day_name', $current_day_name, PDO::PARAM_STR);
    $stmt->execute();
    $today_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter classes that are still upcoming today
    $upcoming_classes = [];
    $currentDateTime = new DateTime();

    foreach ($today_classes as $class) {
        // Combine today's date and class time
        $classDateTime = new DateTime($current_date . ' ' . $class['time']);
        $currentDateTime = new DateTime();

        // Check if the class is still upcoming
        if ($classDateTime >= $currentDateTime) {
            $upcoming_classes[] = [
                'class_date' => $current_date,
                'day' => $current_day_name,
                'time' => $class['time'],
                'course_name' => $class['course_name'],
                'grade_name' => $class['grade_name'],
                'zoom_link' => $class['zoom_link']
            ];
        }
        if (count($upcoming_classes) >= 4) break; // Stop at 4 classes
    }
} catch (PDOException $e) {
    die("Error fetching today's classes: " . $e->getMessage());
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
        header("Location: student_request.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating request status: " . $e->getMessage());
    }
}

// Handle accept/reject actions for time slot requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_slot_request_id'], $_POST['action'])) {
    $time_slot_request_id = $_POST['time_slot_request_id'];
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['accept', 'reject'])) {
        die("Invalid action.");
    }

    // Update the status of a time slot request
try {
    $stmt = $pdo->prepare("
        UPDATE time_slot_request tsr
        JOIN time_slot ts ON tsr.time_slot_id = ts.time_slot_id
        SET tsr.status = :status
        WHERE tsr.time_slot_request_id = :time_slot_request_id AND ts.tutor_id = :tutor_id
    ");
    $stmt->execute([
        ':status' => $action === 'accept' ? 'accepted' : 'rejected',
        ':time_slot_request_id' => $time_slot_request_id,
        ':tutor_id' => $tutor_id,
    ]);

    // Redirect to avoid form resubmission
    header("Location: tutor_dashboard.php");
    exit();
} catch (PDOException $e) {
    die("Error updating time slot request status: " . $e->getMessage());
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
                <li><a href="time_request.php"><i class="fas fa-user-plus"></i> Time slot Requests</a></li>
            </div>

        <div class="sidebar3">
        <li><a href="view_announcement.php">View Announcements</a></li>
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
                <div class="class-schedule">
                    <?php if (!empty($upcoming_classes)): ?>
                        <?php foreach ($upcoming_classes as $class): ?>
                            <div class="class-item">
                                <span>
                                    <?= htmlspecialchars($class['class_date']) ?> (<?= htmlspecialchars($class['day']) ?>),
                                    <?= htmlspecialchars($class['time']) ?> - 
                                    <?= htmlspecialchars($class['course_name']) ?> (Grade <?= htmlspecialchars($class['grade_name']) ?>)
                                </span>
                                <?php
                                // Combine today's date and class time
                                $classDateTime = new DateTime($class['class_date'] . ' ' . $class['time']);
                                $currentDateTime = new DateTime();

                                // Calculate the time difference in seconds
                                $timeDifference = $classDateTime->getTimestamp() - $currentDateTime->getTimestamp();

                                // Enable the button if the class starts in 5 minutes or less
                                $isJoinEnabled = $timeDifference <= 300 && $timeDifference > 0;
                                ?>
                                <a href="<?= htmlspecialchars($class['zoom_link']) ?>" target="_blank">
                                    <button class="join-now" <?= $isJoinEnabled ? '' : 'disabled' ?>>Join Now</button>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No classes scheduled for today.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section class="student-requests">
                <h3>Student Requests</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>View Profile</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                            <td><a href="view_student.php?student_id=<?= htmlspecialchars($request['student_id']) ?>" class="view-profile">View Profile</a></td>
                            <td><?= htmlspecialchars($request['date']) ?></td>
                            <td>
                                <form method="POST" action="student_request.php" style="display: inline;">
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

        <section class="time-slot-requests">
            <h3>Time Slot Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($time_slot_requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                        <td><?= htmlspecialchars($request['grade']) ?></td>
                        <td><?= htmlspecialchars($request['name']) ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="time_slot_request_id" value="<?= htmlspecialchars($request['time_slot_request_id']) ?>">
                                <button type="submit" name="action" value="accept" class="btn accept">Accept</button>
                                <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="view submissions">
            <h2>Recent Submissions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Assignment Title</th>
                        <th>Submission Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_submissions as $submission): ?>
                    <tr>
                        <td><?= htmlspecialchars($submission['student_first_name'] . ' ' . $submission['student_last_name']) ?></td>
                        <td><?= htmlspecialchars($submission['assignment_title']) ?></td>
                        <td><?= htmlspecialchars($submission['submission_date']) ?></td>
                        <td>
                            <a href="view_submission.php?assignment_id=<?= htmlspecialchars($submission['assignment_id']) ?>" class="btn">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>

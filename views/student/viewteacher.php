<?php
session_start();
require '../db.php'; 


if (!isset($_GET['tutor_id'])) {
    die("Tutor ID not provided.");
}
$tutor_id = $_GET['tutor_id'];


if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];


try {
    $stmtStudent = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $stmtStudent->execute([':user_id' => $user_id]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found.");
    }

    $student_id = $student['student_id'];
} catch (PDOException $e) {
    die("Error fetching student ID: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tutor_id'], $_POST['student_id'])) {
    $tutor_id = $_POST['tutor_id'];
    $student_id = $_POST['student_id'];

    try {
       
        $stmtCheck = $pdo->prepare("
            SELECT tutor_student_request_id 
            FROM tutor_student_request 
            WHERE student_id = :student_id AND tutor_id = :tutor_id
        ");
        $stmtCheck->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);
        $existingRequest = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingRequest) {
            $message = "Request already exists.";
        } else {
           
            $stmtInsert = $pdo->prepare("
                INSERT INTO tutor_student_request (student_id, tutor_id, status, date) 
                VALUES (:student_id, :tutor_id, 'Pending', NOW())
            ");
            $stmtInsert->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);
            $message = "Request sent successfully.";
        }
    } catch (PDOException $e) {
        $message = "Error sending request: " . $e->getMessage();
    }
}


try {
    $stmtCheck = $pdo->prepare("
        SELECT tutor_student_request_id, status
        FROM tutor_student_request 
        WHERE student_id = :student_id AND tutor_id = :tutor_id
    ");
    $stmtCheck->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);
    $existingRequest = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $requestStatus = $existingRequest['status'] ?? null; // Get the status or null if no request exists
} catch (PDOException $e) {
    die("Error fetching request status: " . $e->getMessage());
}


try {
    $stmt = $pdo->prepare("
        SELECT t.fee, t.description, u.profile_photo, u.first_name, u.last_name
        FROM tutor t
        JOIN user u ON t.user_id = u.user_id
        WHERE t.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    $fees = $tutor['fee'] ?? 'N/A';
    $description = $tutor['description'] ?? 'No description available.';
    $profile_photo = $tutor['profile_photo'] ?? 'default-profile.png';
    $tutor_name = $tutor['first_name'] . ' ' . $tutor['last_name'];
} catch (PDOException $e) {
    die("Error fetching tutor details: " . $e->getMessage());
}


try {
    $stmt = $pdo->prepare("
        SELECT day, start_time, end_time
        FROM time_slot
        WHERE tutor_id = :tutor_id AND status = 'available'
        ORDER BY FIELD(day, 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), start_time
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching availability: " . $e->getMessage());
}


try {
    $stmt = $pdo->prepare("
        SELECT f.rating, f.comments
        FROM feedback f
        JOIN grade_class gc ON f.grade_class_id = gc.grade_class_id
        WHERE gc.tutor_id = :tutor_id
        ORDER BY f.feedback_id DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching feedback: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - View Tutor</title>
    <link rel="stylesheet" href="../../assets/css/student/viewteacher.css">
</head>
<body>
   
    <header>
        <?php include '../header_student.php'; ?>
    </header>

   
    <div class="content-wrapper">
        
        <div class="tutor-details">
        <img src="../../<?= htmlspecialchars($tutor['profile_photo'] ?: 'assets/images/studentpropic.png') ?>" alt="Profile Image">
        <h2><?= htmlspecialchars($tutor_name) ?></h2>
            <p>Fee: LKR <?= htmlspecialchars($fees) ?> per month</p>
            <p><?= nl2br(htmlspecialchars($description)) ?></p>
            <form method="POST" action="viewteacher.php?tutor_id=<?= htmlspecialchars($tutor_id) ?>">
                <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($tutor_id) ?>">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
                <button 
                    type="submit" 
                    class="request-btn" 
                    <?= $requestStatus === 'Accepted' || $requestStatus === 'Rejected' ? 'disabled' : '' ?>>
                    <?= $requestStatus ? htmlspecialchars($requestStatus) : 'Send Request' ?>
                </button>
            </form>
            <?php if (isset($message)): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </div>

        
        <div class="availability-section">
            <h3>Availability</h3>
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($availability)): ?>
                        <?php foreach ($availability as $slot): ?>
                            <tr>
                                <td><?= htmlspecialchars($slot['day']) ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($slot['start_time']))) ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($slot['end_time']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No availability found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

       
        <div class="feedback-section">
            <h3>Feedback</h3>
            <?php if (!empty($feedbacks)): ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback">
                        <p><strong>Rating:</strong> <?= htmlspecialchars($feedback['rating']) ?> ★</p>
                        <p><?= nl2br(htmlspecialchars($feedback['comments'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No feedback available.</p>
            <?php endif; ?>
        </div>
    </div>

   
    <?php include '../footer.php'; ?>
</body>
</html>
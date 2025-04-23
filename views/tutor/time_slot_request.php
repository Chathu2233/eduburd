<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}

$tutor_id = $_SESSION['tutor_id'];

// Fetch time slot requests for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT 
            tsr.time_slot_request_id, 
            tsr.student_id, 
            tsr.status, 
            tsr.requested_time, 
            u.first_name, 
            u.last_name 
        FROM 
            time_slot_request tsr
        JOIN 
            student s ON tsr.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            tsr.tutor_id = :tutor_id AND tsr.status = 'pending'
        ORDER BY 
            tsr.requested_time DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $time_slot_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching time slot requests: " . $e->getMessage());
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
            UPDATE time_slot_request 
            SET status = :status 
            WHERE time_slot_request_id = :request_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':status' => $action === 'accept' ? 'accepted' : 'rejected',
            ':request_id' => $request_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Redirect to avoid form resubmission
        header("Location: time_slot_request.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating request status: " . $e->getMessage());
    }
}
?>
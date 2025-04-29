<?php
session_start();
require '../db.php'; // Include the database connection

// Get tutor_id from the URL
if (!isset($_GET['tutor_id'])) {
    die("Tutor ID not provided.");
}
$tutor_id = $_GET['tutor_id'];

// Get the logged-in student's ID
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];


$tutor_id = $_POST['tutor_id'];
$student_id = $_POST['student_id'];

// Check if a request already exists
try {
    $stmtCheck = $pdo->prepare("
        SELECT tutor_student_request_id 
        FROM tutor_student_request 
        WHERE student_id = :student_id AND tutor_id = :tutor_id
    ");
    $stmtCheck->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);
    $existingRequest = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        echo json_encode(['success' => false, 'message' => 'Request already exists.']);
        exit;
    }

    // Insert a new request into the tutor_student_request table
    $stmtInsert = $pdo->prepare("
        INSERT INTO tutor_student_request (student_id, tutor_id, status, date) 
        VALUES (:student_id, :tutor_id, 'Pending', NOW())
    ");
    $stmtInsert->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Request sent successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error sending request: ' . $e->getMessage()]);
}
?>

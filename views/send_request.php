<?php
session_start();
require 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['tutor_id'], $_POST['student_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing tutor_id or student_id.']);
        exit();
    }

    $tutor_id = $_POST['tutor_id'];
    $student_id = $_POST['student_id'];
    $status = 'pending';
    $date = date('Y-m-d H:i:s'); // Current date and time

    try {
        // Check if a request already exists
        $stmtCheck = $pdo->prepare("
            SELECT tutor_student_request_id 
            FROM tutor_student_request 
            WHERE student_id = :student_id AND tutor_id = :tutor_id
        ");
        $stmtCheck->execute([':student_id' => $student_id, ':tutor_id' => $tutor_id]);
        $existingRequest = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingRequest) {
            echo json_encode(['success' => false, 'message' => 'Request already sent.']);
            exit();
        }

        // Insert a new request into the tutor_student_request table
        $stmtInsert = $pdo->prepare("
            INSERT INTO tutor_student_request (student_id, tutor_id, status, date)
            VALUES (:student_id, :tutor_id, :status, :date)
        ");
        $stmtInsert->execute([
            ':student_id' => $student_id,
            ':tutor_id' => $tutor_id,
            ':status' => $status,
            ':date' => $date
        ]);

        echo json_encode(['success' => true, 'message' => 'Request sent successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
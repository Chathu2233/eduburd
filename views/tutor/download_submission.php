<?php
session_start();
require '../db.php'; // Include the database connection

// Check if submission_id is provided
if (!isset($_GET['submission_id'])) {
    die("Submission ID not provided.");
}

$submission_id = $_GET['submission_id'];

try {
    // Fetch the file content and metadata from the database
    $stmt = $pdo->prepare("
        SELECT file, file_name 
        FROM assignment_submission 
        WHERE assignment_submission_id = :submission_id
    ");
    $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
    $stmt->execute();
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        die("File not found.");
    }

    $file_content = $submission['file'];
    $file_name = $submission['file_name'];

    // Serve the file as a download
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . basename($file_name));
    header("Content-Length: " . strlen($file_content));
    echo $file_content;
    exit();
} catch (PDOException $e) {
    die("Error fetching file: " . $e->getMessage());
}
?>
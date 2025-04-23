<?php
require_once '../db.php';

$assignment_id = $_GET['assignment_id'] ?? null;
$student_id = $_GET['student_id'] ?? null;

if ($assignment_id && $student_id) {
    $stmt = $pdo->prepare("
        SELECT file 
        FROM assignment_submission 
        WHERE assignment_id = :assignment_id AND student_id = :student_id
    ");
    $stmt->execute([
        ':assignment_id' => $assignment_id,
        ':student_id' => $student_id,
    ]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($submission && $submission['file']) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="submission_' . $assignment_id . '_' . $student_id . '.pdf"');
        echo $submission['file'];
        exit;
    }
}

echo "File not found.";
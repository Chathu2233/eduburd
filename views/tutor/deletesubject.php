<?php
session_start();
require_once '../constants.php';
include '../db.php';

// Ensure the tutor is logged in and get the tutor_id
if (!isset($_SESSION['tutor_id'])) {
    $_SESSION['error'] = "You must be logged in as a tutor to delete subjects.";
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = trim($_POST['course_id']);

    // Validate input
    if (empty($course_id)) {
        $_SESSION['error'] = "Invalid course ID.";
        header("Location: subject.php");
        exit();
    }

    try {
        // Delete the subject from tutor_subject table
        $stmt = $pdo->prepare("DELETE FROM tutor_subject WHERE tutor_id = :tutor_id AND course_id = :course_id");
        $stmt->bindParam(':tutor_id', $tutor_id);
        $stmt->bindParam(':course_id', $course_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting subject.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect back to the subjects page
    header("Location: subject.php");
    exit();
} else {
    header("Location: subject.php");
    exit();
}
?>
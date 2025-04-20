<?php
session_start();
require_once '../db.php'; // Include the database connection file

// Ensure the user is logged in and is an admin
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_notifications') {
        // Clear notifications logic (e.g., mark all as read in the database)
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE audience = 'admin'");
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Notifications cleared.']);
        exit;
    }

    $notifications = [];

    // Query 1: New User Registrations
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name, user_role, created_at 
                           FROM user 
                           WHERE created_at >= NOW() - INTERVAL 1 DAY");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        $notifications[] = "New user registered: {$user['name']} (Role: {$user['user_role']})";
    }

    // Query 2: New Announcements (Updated for `admin_announcement` table)
    $stmt = $pdo->prepare("SELECT text, audience, date 
                           FROM admin_announcement 
                           WHERE date >= NOW() - INTERVAL 1 DAY");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($announcements as $announcement) {
        $notifications[] = "New announcement for {$announcement['audience']}: '{$announcement['text']}'";
    }

    // Query 3: New Assignments
    $stmt = $pdo->prepare("SELECT title, deadline 
                           FROM assignment 
                           WHERE deadline >= NOW() - INTERVAL 1 DAY");
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($assignments as $assignment) {
        $notifications[] = "New assignment added: '{$assignment['title']}' (Deadline: {$assignment['deadline']})";
    }

    // Query 4: Pending Tutor Approvals
    $stmt = $pdo->prepare("SELECT tutor_admin_request_id 
                           FROM tutor_admin_request 
                           WHERE status = 0");
    $stmt->execute();
    $pendingApprovals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pendingApprovals as $approval) {
        $notifications[] = "Pending tutor approval: Request ID {$approval['tutor_admin_request_id']}";
    }

    // Return notifications as JSON
    echo json_encode($notifications);
} else {
    // If not logged in or not an admin, return an empty array
    echo json_encode([]);
}
?>


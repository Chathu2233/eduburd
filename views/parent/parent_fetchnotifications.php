<?php
session_start();
require_once '../db.php'; // Include the database connection file

// Ensure the user is logged in and is a parent
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'parent') {
    try {
        // Fetch unread notifications for parents
        $stmt = $pdo->prepare("SELECT admin_announcement_id, text 
                               FROM admin_announcement 
                               WHERE audience = 'parent' AND is_read = 0");
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark notifications as read
        if (!empty($notifications)) {
            $stmt = $pdo->prepare("UPDATE admin_announcement 
                                   SET is_read = 1 
                                   WHERE audience = 'parent' AND is_read = 0");
            $stmt->execute();
        }

        // Return notifications as JSON
        echo json_encode($notifications);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to fetch notifications.']);
    }
} else {
    // If not logged in or not a parent, return an empty array
    echo json_encode([]);
}
?>
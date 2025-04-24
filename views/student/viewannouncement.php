<?php 
session_start();
require '../db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Fetch announcements for the "student" audience
try {
    $stmt = $pdo->prepare("
        SELECT text, date 
        FROM admin_announcement 
        WHERE audience = 'student'
        ORDER BY date DESC
    ");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd Announcements</title>
    <link rel="stylesheet" href="../../assets/css/student/viewannouncement.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Announcement Content -->
        <section class="announcements">
            <h1>General News and Announcements</h1>
            <table>
                <thead>
                    <tr>
                        <th>Announcements</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="2">No announcements available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($announcement['text']); ?></td>
                                <td><?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($announcement['date']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

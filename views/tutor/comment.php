<?php
session_start();
require '../db.php'; // Include the database connection

// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id'];

// Handle reply, edit, or delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['parent_comment_id'], $_POST['reply'])) {
        // Handle reply or edit
        $parent_comment_id = $_POST['parent_comment_id'];
        $reply = trim($_POST['reply']);

        try {
            $stmt = $pdo->prepare("
                UPDATE parent_comment
                SET reply = :reply
                WHERE parent_comment_id = :parent_comment_id
            ");
            $stmt->execute([
                ':reply' => $reply,
                ':parent_comment_id' => $parent_comment_id,
            ]);

            // Set success message
            if (empty($_POST['action']) || $_POST['action'] === 'reply') {
                $_SESSION['success_message'] = "Reply added successfully!";
            } else {
                $_SESSION['success_message'] = "Reply edited successfully!";
            }

            // Redirect to avoid form resubmission
            header("Location: comment.php?grade_class_id=" . urlencode($grade_class_id));
            exit();
        } catch (PDOException $e) {
            die("Error updating reply: " . $e->getMessage());
        }
    } elseif (isset($_POST['delete_reply_id'])) {
        // Handle delete reply
        $parent_comment_id = $_POST['delete_reply_id'];

        try {
            $stmt = $pdo->prepare("
                UPDATE parent_comment
                SET reply = NULL
                WHERE parent_comment_id = :parent_comment_id
            ");
            $stmt->execute([
                ':parent_comment_id' => $parent_comment_id,
            ]);

            // Redirect to avoid form resubmission
            header("Location: comment.php?grade_class_id=" . urlencode($grade_class_id));
            exit();
        } catch (PDOException $e) {
            die("Error deleting reply: " . $e->getMessage());
        }
    }
}

// Fetch comments for the specific grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT parent_comment_id, comment, reply, created_at
        FROM parent_comment
        WHERE grade_class_id = :grade_class_id
        ORDER BY created_at DESC
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching comments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Comments</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/addcomment.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/classschedule.css"> <!-- Include sidebar styles -->
    <script>
        function confirmDeleteReply() {
            return confirm("Are you sure you want to delete this reply?");
        }
    </script>
</head>
<body>
<header class="navbar">
    <?php include '../header_tutor.php'; ?>
</header>
<div class="content-wrapper" style="display: flex; align-items: flex-start; gap: 20px;">
    <!-- Sidebar Section -->
    <div class="sidebar">
    <a href="classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>">
        <img src="../../assets/images/dashboard.png" alt="Dashboard" width="50" height="50" style="margin-top: 30px;">
    </a>        <ul>
            <div class="sidebar1">
                <li><a href="my_account.php"><i class="fas fa-user"></i> My Profile</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="contact_parent.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-user-plus"></i> Contact Parent</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="view_student.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-edit"></i> Student Profile</a></li>
            </div>
            <div class="sidebar4">
                <li><a href="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-edit"></i> Parent Comments</a></li>
            </div>
            <div class="sidebar5">
                <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            </div>
            <div class="sidebar6">
                <li><a href="../resourcelibrary.php"><i class="fas fa-credit-card"></i> Resource Library</a></li>
            </div>
        </ul>
    </div>

    <!-- Main Content Section -->
    <div class="container">
        <h1>Comments from Parents</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
        <?php endif; ?>

        <div class="comments-section">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <h3>Comment</h3>
                        <p><strong>Comment:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
                        <?php if (empty($comment['reply'])): ?>
                            <!-- Show typing section and Submit Reply button if no reply exists -->
                            <form action="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" method="POST" class="reply-form">
                                <textarea name="reply" rows="2" placeholder="Write your reply..."></textarea>
                                <input type="hidden" name="parent_comment_id" value="<?= htmlspecialchars($comment['parent_comment_id']) ?>">
                                <button type="submit" name="action" value="reply" class="reply-btn">Submit Reply</button>
                            </form>
                        <?php else: ?>
                            <!-- Show comment, reply, Edit Reply button, and Delete Reply button if reply exists -->
                            <p><strong>Reply:</strong> <?= htmlspecialchars($comment['reply']) ?></p>
                            <p><small><em>Posted on: <?= htmlspecialchars($comment['created_at']) ?></em></small></p>

                            <!-- Edit Reply Form -->
                            <form action="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" method="POST" class="reply-form">
                                <textarea name="reply" rows="2"><?= htmlspecialchars($comment['reply']) ?></textarea>
                                <input type="hidden" name="parent_comment_id" value="<?= htmlspecialchars($comment['parent_comment_id']) ?>">
                                <div class="actions">
                                    <button type="submit" name="action" value="edit" class="edit-btn">Edit Reply</button>
                                </div>
                            </form>

                            <!-- Delete Reply Button -->
                            <form action="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" method="POST" class="delete-form" onsubmit="return confirmDeleteReply();">
                                <input type="hidden" name="delete_reply_id" value="<?= htmlspecialchars($comment['parent_comment_id']) ?>">
                                <button type="submit" class="delete-btn">Delete Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments available for this class.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
</body>
</html>

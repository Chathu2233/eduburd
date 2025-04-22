<?php
session_start();
require_once '../constants.php';
require '../db.php'; // Use db.php for database connection

// Fetch tutor_id and tutor's full name (assuming tutor_id is predefined or fetched from session)
$tutor_id = isset($_SESSION['tutor_id']) ? intval($_SESSION['tutor_id']) : null;
if (!$tutor_id) {
    die('Tutor ID is required.');
}

// Fetch tutor details using tutor_id
$query = "
    SELECT t.tutor_id, u.first_name, u.last_name 
    FROM tutor t
    JOIN user u ON t.user_id = u.user_id
    WHERE t.tutor_id = :tutor_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die('Tutor not found for the given Tutor ID.');
}

$tutor_name = htmlspecialchars($result['first_name'] . ' ' . $result['last_name']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $comment = htmlspecialchars($_POST['comment']);

        // Insert comment into parent_comment table
        $insertQuery = "
            INSERT INTO parent_comment (comment, reply, created_at)
            VALUES (:comment, '', NOW())
        ";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        if ($insertStmt->execute()) {
            $_SESSION['success_message'] = 'Comment added successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to add comment.';
        }

        // Redirect to avoid form resubmission
        header("Location: addcomment.php");
        exit;
    } elseif ($_POST['action'] === 'delete') {
        $comment_id = intval($_POST['comment_id']);

        // Delete comment from parent_comment table
        $deleteQuery = "DELETE FROM parent_comment WHERE parent_comment_id = :comment_id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = 'Comment deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete comment.';
        }

        // Redirect to avoid form resubmission
        header("Location: addcomment.php");
        exit;
    } elseif ($_POST['action'] === 'edit') {
        $comment_id = intval($_POST['comment_id']);
        $updated_comment = htmlspecialchars($_POST['updated_comment']);

        // Update comment in parent_comment table
        $updateQuery = "UPDATE parent_comment SET comment = :updated_comment WHERE parent_comment_id = :comment_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':updated_comment', $updated_comment, PDO::PARAM_STR);
        $updateStmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = 'Comment updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update comment.';
        }

        // Redirect to avoid form resubmission
        header("Location: addcomment.php");
        exit;
    }
}

// Fetch comments
$commentsQuery = "
    SELECT pc.parent_comment_id, pc.created_at, pc.comment, pc.reply, 
           CASE WHEN pc.reply = '' THEN 'no reply yet' ELSE 'replied' END AS status
    FROM parent_comment pc
    ORDER BY pc.created_at DESC
";
$commentsStmt = $pdo->prepare($commentsQuery);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Comments</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/addcomment.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <script>
        function enableEdit(commentId) {
            const commentText = document.getElementById(`comment-text-${commentId}`);
            const editForm = document.getElementById(`edit-form-${commentId}`);
            commentText.style.display = 'none';
            editForm.style.display = 'block';
        }
    </script>
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <main class="main-content">
            <div class="container">
                <h2>Parent Comments</h2>

                <!-- Display Success or Error Messages -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>

                <!-- Comment Form -->
                <div class="comment-form-container">
                    <h3 class="form-title"> Add a comment</h3>
                    <form method="POST" action="addcomment.php" class="comment-form">
                        <input type="hidden" name="action" value="add">

                        <div class="form-field">
                            <label for="tutor_name">Tutor Name</label>
                            <input type="text" id="tutor_name" value="<?php echo $tutor_name; ?>" readonly>
                        </div>

                        <div class="form-field">
                            <label for="comment">Comment</label>
                            <textarea id="comment" name="comment" rows="4" required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Add comment</button>
                    </form>
                </div>

                <!-- Comments Table -->
                <div class="comment-table-container">
                    <h3 style="margin-top: 30px;">All Comments</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Comment</th>
                                <th>Tutor Reply</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($comments)): ?>
                                <tr>
                                    <td colspan="5">No comments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                                        <td>
                                            <span id="comment-text-<?php echo $comment['parent_comment_id']; ?>"><?php echo htmlspecialchars($comment['comment']); ?></span>
                                            <form method="POST" action="addcomment.php" id="edit-form-<?php echo $comment['parent_comment_id']; ?>" style="display:none;">
                                                <input type="hidden" name="action" value="edit">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['parent_comment_id']; ?>">
                                                <textarea name="updated_comment" rows="2"><?php echo htmlspecialchars($comment['comment']); ?></textarea>
                                                <button type="submit" class="btn save-btn">Save</button>
                                            </form>
                                        </td>
                                        <td><?php echo htmlspecialchars($comment['reply'] ?? 'No reply yet'); ?></td>
                                        <td><?php echo htmlspecialchars($comment['status']); ?></td>
                                        <td>
                                            <?php if ($comment['status'] === 'no reply yet'): ?>
                                                <div class="actions">
                                                    <form method="POST" action="addcomment.php" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="comment_id" value="<?php echo $comment['parent_comment_id']; ?>">
                                                        <button type="submit" class="btn delete-btn">Delete</button>
                                                    </form>
                                                    <button class="btn edit-btn" onclick="enableEdit(<?php echo $comment['parent_comment_id']; ?>)">Edit</button>
                                                </div>
                                            <?php else: ?>
                                                <span>--</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
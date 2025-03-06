<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Comments</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/addcomment.css">
</head>
<body>
<header class="navbar">
    <?php include '../header_tutor.php'; ?>
</header>
<div class="container">
    <h1>Comments from Parents</h1>

    <div class="comment">
        <h3>From: John Doe</h3>
        <p><strong>Comment:</strong> My child is loving the classes. Great job!</p>
        <p><strong>Reply:</strong> Thank you for the kind words!</p>

        <form action="" method="POST" class="reply-form">
            <textarea name="reply" rows="2" placeholder="Write a reply...">Thank you for the kind words!</textarea>
            <div class="actions">
                <button type="submit" name="action" value="reply" class="reply-btn">Reply</button>
                <button type="submit" name="action" value="edit" class="edit-btn">Edit Reply</button>
                <button type="submit" name="action" value="delete" class="delete-btn">Delete Reply</button>
            </div>
        </form>
        </div>

    <div class="comment">
        <h3>From: Jane Smith</h3>
        <p><strong>Comment:</strong> Can you provide additional resources for math?</p>
        <p><strong>Reply:</strong> No reply yet.</p>

        <form action="" method="POST" class="reply-form">
            <textarea name="reply" rows="2" placeholder="Write a reply..."></textarea>
            <div class="actions">
                <button type="submit" name="action" value="reply" class="reply-btn">Reply</button>
                <button type="submit" name="action" value="edit" class="edit-btn">Edit Reply</button>
                <button type="submit" name="action" value="delete" class="delete-btn">Delete Reply</button>
            </div>
        </form>
    </div>
</div>
<?php include '../footer.php'; ?>
</body>
</html>

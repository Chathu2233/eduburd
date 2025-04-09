<?php
session_start();
require_once '../constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Comments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/addcomment.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Parent Comments</h2>
                <!-- Comment Form -->
                <form id="comment-form">
                    <input type="text" id="topic" placeholder="Enter Topic" required>
                    <textarea id="comment" rows="4" placeholder="Enter Comment" required></textarea>
                    <button type="submit" class="btn-primary">Add Comment</button>
                </form>

                <!-- Comments Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Topic</th>
                            <th>Comment</th>
                            <th>Teacher Reply</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="comment-table-body">
                        <tr>
                            <td>2024-11-29 10:00 AM</td>
                            <td>Worksheets</td>
                            <td>Could you please provide more practice worksheets for grammar? My child finds them very helpful.</td>
                            <td>I will look into it</td>
                        </tr>
                        <!-- Comments will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script>
        // Initialize an empty comments array
        const comments = [];

        // Add event listener to the form
        document.getElementById('comment-form').addEventListener('submit', addComment);

        // Function to add a new comment
        function addComment(event) {
            event.preventDefault(); // Prevent form submission

            const topic = document.getElementById('topic').value;
            const commentText = document.getElementById('comment').value;

            // Create a comment object
            const comment = {
                id: Date.now(), // Unique ID
                topic,
                text: commentText,
                date: new Date().toLocaleString(),
                teacherReply: null // Initially no reply
            };

            comments.push(comment); // Add to comments array
            renderTable(); // Refresh the table

            // Clear the form
            document.getElementById('topic').value = '';
            document.getElementById('comment').value = '';
        }

        // Function to render the table
        function renderTable() {
            const tableBody = document.getElementById('comment-table-body');
            tableBody.innerHTML = ''; // Clear the table body

            comments.forEach((comment) => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${comment.date}</td>
                    <td>${comment.topic}</td>
                    <td>${comment.text}</td>
                    <td>${comment.teacherReply || "No reply yet"}</td>
                    <td class="actions">
                        ${
                            comment.teacherReply
                                ? '<span class="no-actions">No actions allowed</span>'
                                : `
                                    <button class="edit-btn" onclick="editComment(${comment.id})">Edit</button>
                                    <button class="delete-btn" onclick="deleteComment(${comment.id})">Delete</button>
                                  `
                        }
                    </td>
                `;

                tableBody.appendChild(row);
            });
        }

        // Function to edit a comment
        function editComment(commentId) {
            const comment = comments.find(c => c.id === commentId);

            const newTopic = prompt('Edit Topic:', comment.topic);
            const newText = prompt('Edit Comment:', comment.text);

            if (newTopic !== null && newText !== null) {
                comment.topic = newTopic;
                comment.text = newText;
                renderTable();
            }
        }

        // Function to delete a comment
        function deleteComment(commentId) {
            const index = comments.findIndex(c => c.id === commentId);
            if (index !== -1 && confirm('Are you sure you want to delete this comment?')) {
                comments.splice(index, 1); // Remove the comment
                renderTable();
            }
        }

        // Simulate a teacher reply for testing purposes
        setTimeout(() => {
            if (comments.length > 0) {
                comments[0].teacherReply = "Thank you for your feedback!";
                renderTable();
            }
        }, 5000); // Add a reply after 5 seconds
    </script>
</body>
</html>
<?php
session_start();

require '../db.php'; // Include database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tutor_name = $_POST['tutor_name'];
    $course_name = $_POST['course_name'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO feedback (tutor_name, course_name, rating, comments) 
            VALUES (:tutor_name, :course_name, :rating, :comments)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':tutor_name' => $tutor_name,
            ':course_name' => $course_name,
            ':rating' => $rating,
            ':comments' => $comments
        ]);
        $success_message = "Feedback submitted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error submitting feedback: " . $e->getMessage();
    }
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM feedback WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([':id' => $delete_id]);
        $success_message = "Feedback deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error deleting feedback: " . $e->getMessage();
    }
}

// Fetch feedback to display
$sql = "SELECT id, tutor_name, course_name, rating, comments FROM feedback";
$stmt = $pdo->query($sql);
$feedback_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback Form</title>
    <link rel="stylesheet" href="../../assets/css/student/feedback.css">
    
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <div class="container">
        <h1>Submit Your Feedback</h1>

        <!-- Display Messages -->
        <?php if (!empty($success_message)): ?>
            <p class="message success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php elseif (!empty($error_message)): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- Feedback Form -->
        <form method="POST" action="feedback.php">
            <div class="form-group">
                <label for="tutor_name">Tutor Name</label>
                <input type="text" id="tutor_name" name="tutor_name" placeholder="Enter the tutor's name" required>
            </div>

            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" id="course_name" name="course_name" placeholder="Enter the course name" required>
            </div>

            <div class="form-group">
                <label for="rating">Rating (1-5)</label>
                <input type="number" id="rating" name="rating" min="1" max="5" placeholder="Give a rating (1-5)" required>
            </div>

            <div class="form-group">
                <label for="comments">Comments</label>
                <textarea id="comments" name="comments" rows="4" placeholder="Share your feedback" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>

        <!-- Feedback Table -->
        <h2>Submitted Feedback</h2>
        <table>
            <thead>
                <tr>
                    <th>Tutor Name</th>
                    <th>Course Name</th>
                    <th>Rating</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($feedback_list)): ?>
                    <?php foreach ($feedback_list as $feedback): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feedback['tutor_name']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                            <td>
                                <a href="feedbackedit.php?id=<?php echo $feedback['id']; ?>" class="edit-btn">Edit</a>
                                <a href="?delete_id=<?php echo $feedback['id']; ?>" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No feedback submitted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
<script>
        function hideMessage() {
            var messages = document.querySelectorAll('.message');
            messages.forEach(function (message) {
                setTimeout(function () {
                    message.style.opacity = 0;
                }, 10000); // 10 seconds
            });
        }
        window.onload = hideMessage;
    </script>
</html>

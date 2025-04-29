<?php
session_start();

require '../db.php'; // Include database connection

// Check if grade_class_id is passed
if (!isset($_GET['grade_class_id'])) {
    die("Missing grade_class_id.");
}

$grade_class_id = $_GET['grade_class_id'];

try {
    // Fetch tutor and course details based on grade_class_id
    $stmt = $pdo->prepare("
        SELECT 
            CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
            c.name AS course_name,
            gc.day,
            gc.time,
            gc.description
        FROM grade_class gc
        JOIN tutor t ON gc.tutor_id = t.tutor_id
        JOIN user u ON t.user_id = u.user_id
        JOIN course c ON gc.course_id = c.course_id
        WHERE gc.grade_class_id = :grade_class_id
    ");
    $stmt->execute([':grade_class_id' => $grade_class_id]);
    $class_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class_details) {
        die("Class details not found.");
    }

    $tutor_name = $class_details['tutor_name'];
    $course_name = $class_details['course_name'];

    // Fetch feedback for the grade_class_id
    $stmt_feedback = $pdo->prepare("
        SELECT feedback_id, rating, comments 
        FROM feedback 
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt_feedback->execute([':grade_class_id' => $grade_class_id]);
    $feedback_list = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grade_class_id = $_POST['grade_class_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO feedback (grade_class_id, rating, comments) 
            VALUES (:grade_class_id, :rating, :comments)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':grade_class_id' => $grade_class_id,
        ':rating' => $rating,
        ':comments' => $comments
    ]);
    $success_message = "Feedback submitted successfully!";
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM feedback WHERE feedback_id = :feedback_id";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([':feedback_id' => $delete_id]);
        $success_message = "Feedback deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error deleting feedback: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback</title>
    <link rel="stylesheet" href="../../assets/css/student/feedback.css">
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

        <!-- Parent Content -->
        <main class="dashboard">
<section>
<div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>

        
            <section class="feedback-section">
                <h1>Submit Your Feedback</h1>

                <!-- Display Messages -->
                <?php if (!empty($success_message)): ?>
                    <p class="message success"><?php echo htmlspecialchars($success_message); ?></p>
                <?php elseif (!empty($error_message)): ?>
                    <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <!-- Feedback Form -->
                <form method="POST" action="feedback.php?grade_class_id=<?php echo htmlspecialchars($grade_class_id); ?>">
                    <input type="hidden" name="grade_class_id" value="<?php echo htmlspecialchars($grade_class_id); ?>">

                    <div class="form-group">
                        <label for="tutor_name">Tutor Name</label>
                        <input type="text" id="tutor_name" name="tutor_name" value="<?php echo htmlspecialchars($tutor_name); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="course_name">Course Name</label>
                        <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5">
                            <label for="star5" title="5 stars">★</label>

                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 stars">★</label>

                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 stars">★</label>

                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 stars">★</label>

                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 star">★</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comments">Comments</label>
                        <textarea id="comments" name="comments" rows="4" placeholder="Share your feedback" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Submit Feedback</button>
                </form>
            </section>

            <!-- Feedback Table -->
            <section class="feedback-table">
                <h2>Submitted Feedback</h2>
                <table>
    <thead>
        <tr>
            <th>Rating</th>
            <th>Comments</th> <!-- Middle column -->
            <th>Actions</th>  <!-- Last column -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($feedback_list)): ?>
            <?php foreach ($feedback_list as $feedback): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                    <td>
                        <a href="feedbackedit.php?feedback_id=<?php echo $feedback['feedback_id']; ?>&grade_class_id=<?php echo htmlspecialchars($grade_class_id); ?>" class="edit-btn">Edit</a>
                        <a href="?delete_id=<?php echo $feedback['feedback_id']; ?>&grade_class_id=<?php echo htmlspecialchars($grade_class_id); ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Are you sure you want to delete this feedback?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No feedback submitted yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

                </section>
                
            </main>
            
        </div>
    </div>

    <script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const ratings = document.querySelectorAll('input[name="rating"]');
        let ratingSelected = false;
        ratings.forEach(radio => {
            if (radio.checked) {
                ratingSelected = true;
            }
        });
        if (!ratingSelected) {
            alert('Please select a rating before submitting.');
            event.preventDefault();
        }
    });
</script>


   
    <!-- Footer -->

        <?php include '../footer.php'; ?>

</body>
</html>
\
<?php 
session_start();

require '../db.php'; // Include database connection

// Check if feedback_id and grade_class_id are passed
if (!isset($_GET['feedback_id']) || !isset($_GET['grade_class_id'])) {
    die("Missing feedback_id or grade_class_id.");
}

$feedback_id = $_GET['feedback_id'];
$grade_class_id = $_GET['grade_class_id'];

// ✅ Initialize success message from SESSION
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear after showing
}

try {
    // Fetch feedback details
    $stmt = $pdo->prepare("SELECT rating, comments FROM feedback WHERE feedback_id = :feedback_id");
    $stmt->execute([':feedback_id' => $feedback_id]);
    $feedback = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$feedback) {
        die("Feedback not found.");
    }

    // Fetch tutor and course details based on grade_class_id
    $stmt_class = $pdo->prepare("
        SELECT 
            CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
            c.name AS course_name
        FROM grade_class gc
        JOIN tutor t ON gc.tutor_id = t.tutor_id
        JOIN user u ON t.user_id = u.user_id
        JOIN course c ON gc.course_id = c.course_id
        WHERE gc.grade_class_id = :grade_class_id
    ");
    $stmt_class->execute([':grade_class_id' => $grade_class_id]);
    $class_details = $stmt_class->fetch(PDO::FETCH_ASSOC);

    if (!$class_details) {
        die("Class details not found.");
    }

    $tutor_name = $class_details['tutor_name'];
    $course_name = $class_details['course_name'];

    // Handle form submission for updating feedback
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $rating = $_POST['rating'];
        $comments = $_POST['comments'];

        $update_stmt = $pdo->prepare("UPDATE feedback SET rating = :rating, comments = :comments WHERE feedback_id = :feedback_id");
        $update_stmt->execute([
            ':rating' => $rating,
            ':comments' => $comments,
            ':feedback_id' => $feedback_id
        ]);

        // ✅ Save success message in session
        $_SESSION['success_message'] = "Feedback updated successfully!";
        header("Location: feedbackedit.php?feedback_id=" . urlencode($feedback_id) . "&grade_class_id=" . urlencode($grade_class_id));
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback</title>
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
           

                <h1>Edit Feedback</h1>

                <!-- Simple Message Box -->
        <?php if (!empty($success_message)): ?>
            <div class="message-box success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message-box error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

                <!-- Feedback Edit Form -->
                <form method="POST" action="feedbackedit.php?feedback_id=<?php echo htmlspecialchars($feedback_id); ?>&grade_class_id=<?php echo htmlspecialchars($grade_class_id); ?>">

                    <div class="form-group">
                        <label for="tutor_name">Tutor Name:</label>
                        <input type="text" id="tutor_name" name="tutor_name" value="<?php echo htmlspecialchars($tutor_name); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="course_name">Course Name:</label>
                        <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" <?php echo $feedback['rating'] == 5 ? 'checked' : ''; ?>>
                            <label for="star5" title="5 stars">★</label>

                            <input type="radio" id="star4" name="rating" value="4" <?php echo $feedback['rating'] == 4 ? 'checked' : ''; ?>>
                            <label for="star4" title="4 stars">★</label>

                            <input type="radio" id="star3" name="rating" value="3" <?php echo $feedback['rating'] == 3 ? 'checked' : ''; ?>>
                            <label for="star3" title="3 stars">★</label>

                            <input type="radio" id="star2" name="rating" value="2" <?php echo $feedback['rating'] == 2 ? 'checked' : ''; ?>>
                            <label for="star2" title="2 stars">★</label>

                            <input type="radio" id="star1" name="rating" value="1" <?php echo $feedback['rating'] == 1 ? 'checked' : ''; ?>>
                            <label for="star1" title="1 star">★</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comments">Comments:</label>
                        <textarea id="comments" name="comments" rows="4" required><?php echo htmlspecialchars($feedback['comments']); ?></textarea>
                    </div>

                    <button type="submit" class="update-btn">Update Feedback</button>
                </form>
                <div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>
            </section>
        </main>
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
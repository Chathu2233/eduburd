<?php 
session_start();

require '../db.php';

// Fetch the feedback to edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM feedback WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $feedback = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no feedback found, redirect or show an error
    if (!$feedback) {
        die("Feedback not found.");
    }
}

// Handle form submission for editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_feedback'])) {
    $tutor_name = $_POST['tutor_name'];
    $course_name = $_POST['course_name'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql = "UPDATE feedback SET tutor_name = :tutor_name, course_name = :course_name, rating = :rating, comments = :comments WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':tutor_name' => $tutor_name,
            ':course_name' => $course_name,
            ':rating' => $rating,
            ':comments' => $comments,
            ':id' => $id
        ]);
        $success_message = "Feedback updated successfully!";
        
        // Redirect to feedback.php after successful update
        header("Location: feedback.php");
        exit();  // Ensure the script stops after redirection
    } catch (PDOException $e) {
        $error_message = "Error updating feedback: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback</title>
    <link rel="stylesheet" href="../../assets/css/Student/feedback.css">
</head>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
<body>
    <div class="container">
        <h1>Edit Feedback</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($success_message)): ?>
            <p class="message success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php elseif (!empty($error_message)): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST" action="feedbackedit.php?id=<?php echo $feedback['id']; ?>">
            <label for="tutor_name">Tutor Name:</label>
            <input type="text" id="tutor_name" name="tutor_name" value="<?php echo htmlspecialchars($feedback['tutor_name']); ?>" required>
            
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($feedback['course_name']); ?>" required>
            
            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($feedback['rating']); ?>" required>
            
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" rows="4" required><?php echo htmlspecialchars($feedback['comments']); ?></textarea>
            
            <button type="submit" name="edit_feedback" class="update-btn">Update Feedback</button>
        </form>
    </div>
    <?php include '../footer.php'; ?>  
</body>
</html>

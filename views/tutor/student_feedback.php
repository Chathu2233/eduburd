<?php
session_start();
require '../db.php'; // Include the database connection

// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id'];

// Fetch feedbacks for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT feedback_id, rating, comments
        FROM feedback
        WHERE grade_class_id = :grade_class_id
        ORDER BY feedback_id DESC
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching feedbacks: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedbacks</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/student_feedback.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/sidebar.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>



        <!-- Main Content Section -->
        <div class="content-section">
            <div class="feedback-header">
                <h1>📋 Student Feedbacks</h1>
                <p>View feedback and ratings provided by students for this class.</p>
            </div>

            <?php if (!empty($feedbacks)): ?>
                <div class="feedback-container">
                    <table class="feedback-table">
                        <thead>
                            <tr>
                                <th>Rating</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $feedback): ?>
                                <tr>
                                    <td>
                                        <div class="rating">
                                            <?= str_repeat('⭐', htmlspecialchars($feedback['rating'])) ?>
                                            <span class="rating-text">(<?= htmlspecialchars($feedback['rating']) ?> / 5)</span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($feedback['comments']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-feedback">
                    <p>No feedbacks available for this class.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
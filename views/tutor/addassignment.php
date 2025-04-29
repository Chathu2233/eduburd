<?php
session_start();
require '../db.php'; // Include the database connection

$successMessage = ''; // Variable for success message

// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Grade Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id']; // Get grade_class_id from URL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['assignment-title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $upload = ''; // Default value for upload

    // Handle file upload
    if (isset($_FILES['uploads']) && $_FILES['uploads']['error'] === 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['uploads']['name']);

        if (move_uploaded_file($_FILES['uploads']['tmp_name'], $uploadFile)) {
            $upload = $uploadFile;
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Add the assignment to the database
    try {
        $sql = "INSERT INTO assignment (grade_class_id, title, description, deadline, file) 
                VALUES (:grade_class_id, :title, :description, :deadline, :file)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':grade_class_id' => $grade_class_id,
            ':title' => $title,
            ':description' => $description,
            ':deadline' => $deadline,
            ':file' => $upload,
        ]);

        
        // Store the success message in a session
        $_SESSION['success_message'] = "Assignment added successfully!";
        // Redirect to avoid form resubmission
        header("Location: classschedule.php?grade_class_id=" . htmlspecialchars($grade_class_id));
        exit();
    } catch (PDOException $e) {
        die("Error updating assignment: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Assignment</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/addassignment.css">
</head>
<body>
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <main class="add-assignment-page">
        <section class="assignment-form-container">
            <h1>Add Assignment</h1>

            <form action="addassignment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" method="POST" enctype="multipart/form-data" class="add-assignment-form">
                <label for="assignment-title">Title</label>
                <input type="text" id="assignment-title" name="assignment-title" placeholder="Enter Assignment title" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" required>

                <label for="uploads">Uploads</label>
                <input type="file" id="uploads" name="uploads" required>

                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" required>

                <div class="form-controls">
                    <button type="button" class="cancel-button" onclick="window.location.href='classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>'">Cancel</button>
                    <button type="submit" class="add-button">Add</button>
                </div>
            </form>
        </section>
    </main>

    <?php include '../footer.php'; ?>

    <!-- Modal -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="modal" id="successModal" style="display: flex;">
            <div class="modal-content">
                <h2><?= htmlspecialchars($_SESSION['success_message']) ?></h2>
                <button onclick="closeModal()">OK</button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
    <?php endif; ?>

    <script>
        function closeModal() {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.style.display = 'none';
            }
        }
    </script>

    
</body>
</html>

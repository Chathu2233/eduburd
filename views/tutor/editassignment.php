<?php
session_start();
require '../db.php'; // Include the database connection

$successMessage = ''; // Variable for success message

// Check if assignment_id is provided in the URL
if (!isset($_GET['assignment_id'])) {
    die("Assignment ID not provided.");
}

$assignment_id = $_GET['assignment_id']; // Get assignment_id from URL

// Fetch assignment data for editing
try {
    $stmt = $pdo->prepare("SELECT * FROM assignment WHERE assignment_id = :assignment_id");
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        die("Assignment not found.");
    }

    // Get the grade_class_id from the assignment data
    $grade_class_id = $assignment['grade_class_id'];
} catch (PDOException $e) {
    die("Error fetching assignment: " . $e->getMessage());
}

// Handle form submission for updating the assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['assignment-title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $upload = $assignment['file']; // Default to the existing file

    // Handle file upload if a new file is provided
    if (isset($_FILES['uploads']) && $_FILES['uploads']['error'] === 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['uploads']['name']);

        if (move_uploaded_file($_FILES['uploads']['tmp_name'], $uploadFile)) {
            $upload = $uploadFile; // Update the file path
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Update the assignment in the database
    try {
        $sql = "UPDATE assignment SET title = :title, description = :description, deadline = :deadline, file = :file WHERE assignment_id = :assignment_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':deadline' => $deadline,
            ':file' => $upload,
            ':assignment_id' => $assignment_id,
        ]);

        // Store the success message in a session
        $_SESSION['success_message'] = "Assignment updated successfully!";
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
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/editassignment.css">
</head>
<body>
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <main class="edit-assignment-page">
        <section class="assignment-form-container">
            <h1>Edit Assignment</h1>

            <form action="editassignment.php?assignment_id=<?= htmlspecialchars($assignment_id) ?>" method="POST" enctype="multipart/form-data" class="edit-assignment-form">
                <label for="assignment-title">Title</label>
                <input type="text" id="assignment-title" name="assignment-title" placeholder="Enter Assignment title" value="<?= htmlspecialchars($assignment['title']) ?>" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" value="<?= htmlspecialchars($assignment['description']) ?>" required>

                <label for="uploads">Upload New File (optional)</label>
                <input type="file" id="uploads" name="uploads">

                <?php if (!empty($assignment['file'])): ?>
                    <div class="existing-file">
                        <p>Current file: <a href="<?= htmlspecialchars($assignment['file']) ?>" target="_blank"><?= htmlspecialchars(basename($assignment['file'])) ?></a></p>
                    </div>
                <?php endif; ?>

                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" value="<?= htmlspecialchars($assignment['deadline']) ?>" required>

                <div class="form-controls">
                    <button type="button" class="cancel-button" onclick="window.location.href='classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>'">Cancel</button>
                    <button type="submit" class="edit-button">Update</button>
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

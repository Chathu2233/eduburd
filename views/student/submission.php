<?php

session_start();

require_once '../db.php'; // Include database connection (PDO)

// Set the timezone to match your desired region (e.g., Asia/Colombo)
date_default_timezone_set('Asia/Colombo'); // Adjust as needed

// Handle new file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_submission'])) {
    try {
        $assignment_id = $_POST['assignment_id'];
        $file_name = $_FILES['file']['name'];
        $created_at = date('Y-m-d H:i:s'); // Get current time in 'Y-m-d H:i:s' format

        // File upload logic
        if (!empty($file_name)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($file_name);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                $query = "INSERT INTO assignment_submission (assignment_id, file, created_at) VALUES (:assignment_id, :file, :created_at)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
                $stmt->bindParam(':file', $file_name, PDO::PARAM_STR);
                $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
                $stmt->execute();
                header("Location: submission.php?success=added");
                exit;
            } else {
                $error = "Failed to upload the file.";
            }
        } else {
            $error = "No file selected.";
        }
    } catch (PDOException $e) {
        die("Error adding submission: " . $e->getMessage());
    }
}

// Fetch assignments
try {
    $query = "SELECT assignment_id, title, deadline FROM assignment WHERE deadline > NOW()";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch all submissions
try {
    $query = "SELECT asub.assignment_submission_id, asub.assignment_id, asub.file, asub.created_at, asub.result, a.title, a.deadline 
              FROM assignment_submission asub
              JOIN assignment a ON asub.assignment_id = a.assignment_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_submission'])) {
    try {
        $submission_id = $_POST['delete_submission_id'];

        // Fetch the file to delete
        $query = "SELECT file, assignment_id FROM assignment_submission WHERE assignment_submission_id = :submission_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->execute();
        $file_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file_data && isset($file_data['file'])) {
            $assignment_id = $file_data['assignment_id'];

            // Fetch the deadline for the assignment
            $query = "SELECT deadline FROM assignment WHERE assignment_id = :assignment_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
            $stmt->execute();
            $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

            $current_time = date('Y-m-d H:i:s');
            $deadline = $assignment['deadline'];

            // If the deadline has passed, prevent deletion
            if (strtotime($current_time) > strtotime($deadline)) {
                $error = "You cannot delete the submission after the deadline.";
            } else {
                $file_path = "uploads/" . $file_data['file'];
                // Delete the file from the server
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Delete the submission from the database
                $query = "DELETE FROM assignment_submission WHERE assignment_submission_id = :submission_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
                $stmt->execute();

                header("Location: submission.php?success=deleted");
                exit;
            }
        } else {
            $error = "Submission not found or file missing.";
        }
    } catch (PDOException $e) {
        die("Error deleting submission: " . $e->getMessage());
    }
}

// Handle file replacement (edit submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_submission'])) {
    try {
        $submission_id = $_POST['submission_id'];
        $new_file_name = $_FILES['file']['name'];

        // Fetch the old file to delete
        $query = "SELECT file, assignment_id FROM assignment_submission WHERE assignment_submission_id = :submission_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->execute();
        $file_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($new_file_name)) {
            if ($file_data && isset($file_data['file'])) {
                $assignment_id = $file_data['assignment_id'];

                // Fetch the deadline for the assignment
                $query = "SELECT deadline FROM assignment WHERE assignment_id = :assignment_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
                $stmt->execute();
                $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

                $current_time = date('Y-m-d H:i:s');
                $deadline = $assignment['deadline'];

                // If the deadline has passed, prevent editing
                if (strtotime($current_time) > strtotime($deadline)) {
                    $error = "You cannot edit the submission after the deadline.";
                } else {
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($new_file_name);

                    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                        // Delete the old file if it exists
                        if (isset($file_data['file'])) {
                            $old_file_path = $target_dir . $file_data['file'];
                            if (file_exists($old_file_path)) {
                                unlink($old_file_path);
                            }
                        }

                        // Update the database with the new file
                        $query = "UPDATE assignment_submission SET file = :file WHERE assignment_submission_id = :submission_id";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':file', $new_file_name, PDO::PARAM_STR);
                        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
                        $stmt->execute();

                        header("Location: submission.php?success=edited");
                        exit;
                    } else {
                        $error = "Failed to upload the new file.";
                    }
                }
            } else {
                $error = "File data missing.";
            }
        } else {
            $error = "No file selected.";
        }
    } catch (PDOException $e) {
        die("Error editing submission: " . $e->getMessage());
    }
}

$current_time = date('Y-m-d H:i:s'); // Get current time in 'Y-m-d H:i:s' format
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Submissions</title>
    <link rel="stylesheet" href="../../assets/css/Student/submission.css">
</head>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
    <body>
<div class="container">
    <h1>Assignment Submissions</h1>

    <!-- Display Error Message -->
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Display Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <p class="success" id="success-message">
            <?= htmlspecialchars($_GET['success'] === 'added' ? 'Submission added successfully!' : ($_GET['success'] === 'deleted' ? 'Submission deleted successfully!' : ($_GET['success'] === 'edited' ? 'Submission edited successfully!' : ''))) ?>
        </p>
        <script>
            setTimeout(() => {
                const successMessage = document.getElementById('success-message');
                if (successMessage) successMessage.style.display = 'none';
            }, 10000);
        </script>
    <?php endif; ?>

    <!-- Add Submission Form -->
    <form action="submission.php" method="POST" enctype="multipart/form-data" class="submission-form">
        <label for="assignment_id">Select Assignment:</label>
        <select name="assignment_id" required>
            <?php foreach ($assignments as $assignment): ?>
                <option value="<?= htmlspecialchars($assignment['assignment_id']) ?>">
                    <?= htmlspecialchars($assignment['title']) ?> (Deadline: <?= htmlspecialchars($assignment['deadline']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="file">Upload File:</label>
        <input type="file" name="file" required>

        <button type="submit" name="add_submission" class="submit">Submit</button>
    </form>

    <!-- Existing Submissions -->
    <h2>Existing Submissions</h2>
    <table>
        <thead>
            <tr>
                <th>Assignment</th>
                <th>File</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <?php $deadline_passed = strtotime($current_time) > strtotime($submission['deadline']); ?>
                <tr>
                    <td><?= htmlspecialchars($submission['title']) ?> (Deadline: <?= htmlspecialchars($submission['deadline']) ?>)</td>
                    <td><a href="uploads/<?= htmlspecialchars($submission['file']) ?>" target="_blank">Download</a></td>
                    <td><?= htmlspecialchars($submission['created_at']) ?></td>
                    <td>
                        <?php if ($deadline_passed): ?>
                            <span class="deadline-passed">Deadline Passed</span>
                    <?php else: ?>
        <form action="submission.php" method="POST" enctype="multipart/form-data" class="edit-form">
        <label for="edit-file-<?= $submission['assignment_submission_id'] ?>" class="edit-label">
            Edit Submission:
        </label>
        <input type="file" name="file" id="edit-file-<?= $submission['assignment_submission_id'] ?>" required>
        <input type="hidden" name="submission_id" value="<?= $submission['assignment_submission_id'] ?>">
        <button type="submit" name="edit_submission" class="edit">Save</button>
    </form>

    <!-- Delete Submission Form -->
    <form action="submission.php" method="POST" class="delete-form">
        <input type="hidden" name="delete_submission_id" value="<?= $submission['assignment_submission_id'] ?>">
        <button type="submit" name="delete_submission" class="delete" onclick="return confirm('Are you sure you want to delete this submission?')">
            Delete
        </button>
    </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
<?php include '../footer.php'; ?>  
</html>

<?php

session_start();
require '../db.php'; // Include the database connection

$successMessage = ''; // Variable for success message

// Check if tutorial_id is provided in the URL
if (!isset($_GET['tutorial_id'])) {
    die("Tutorial ID not provided.");
}

$tutorial_id = $_GET['tutorial_id']; // Get tutorial_id from URL

// Fetch tutorial data for editing
try {
    $stmt = $pdo->prepare("SELECT * FROM tutorial WHERE tutorial_id = :tutorial_id");
    $stmt->bindParam(':tutorial_id', $tutorial_id, PDO::PARAM_INT);
    $stmt->execute();
    $tutorial = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tutorial) {
        die("Tutorial not found.");
    }

    // Get the grade_class_id from the tutorial data
    $grade_class_id = $tutorial['grade_class_id'];
} catch (PDOException $e) {
    die("Error fetching tutorial: " . $e->getMessage());
}

// Handle form submission for updating the tutorial
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['tutorial-title'];
    $description = $_POST['description'];
    $upload = $tutorial['upload']; // Default to the existing file

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

    // Update the tutorial in the database
    try {
        $sql = "UPDATE tutorial SET title = :title, description = :description, upload = :upload WHERE tutorial_id = :tutorial_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':upload' => $upload,
            ':tutorial_id' => $tutorial_id,
        ]);

        // Set the success message after the tutorial is updated
        $successMessage = "Tutorial updated successfully!";
    } catch (PDOException $e) {
        die("Error updating tutorial: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tutorial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/edittutorial.css">
</head>
<body>
    <header>
        <?php include '../header_tutor.php'; ?>
    </header>

    <main class="edit-tutorial-page">
        <section class="tutorial-form-container">
            <h1>Edit Tutorial</h1>

            <form action="edit_tutorial.php?tutorial_id=<?= htmlspecialchars($tutorial_id) ?>" method="POST" enctype="multipart/form-data" class="edit-tutorial-form">
                <label for="tutorial-title">Title</label>
                <input type="text" id="tutorial-title" name="tutorial-title" placeholder="Enter Tutorial title" value="<?= htmlspecialchars($tutorial['title']) ?>" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" value="<?= htmlspecialchars($tutorial['description']) ?>" required>

                <label for="uploads">Upload New File (optional)</label>
                <input type="file" id="uploads" name="uploads">

                <?php if (!empty($tutorial['upload'])): ?>
                    <div class="existing-file">
                        <p>Current file: <a href="<?= htmlspecialchars($tutorial['upload']) ?>" target="_blank"><?= htmlspecialchars(basename($tutorial['upload'])) ?></a></p>
                    </div>
                <?php endif; ?>

                <div class="form-controls">
                    <button type="button" class="cancel-button" onclick="window.location.href='classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>'">Cancel</button>
                    <button type="submit" class="edit-button">Update</button>
                </div>
            </form>
        </section>
    </main>

    <?php include '../footer.php'; ?>

    <!-- Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h2 id="successMessage"></h2>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        // Check if there is a success message and show the modal
        const successMessage = "<?php echo $successMessage; ?>";
        if (successMessage) {
            document.getElementById('successMessage').innerText = successMessage;
            document.getElementById('successModal').style.display = 'flex';
        }

        // Close the modal and redirect to class schedule page
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
            window.location.href = "classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"; // Redirect to class schedule with grade_class_id
        }
    </script>
</body>
</html>
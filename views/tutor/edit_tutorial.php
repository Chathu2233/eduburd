<?php

session_start();


// Include the database connection and config file
require '../Database.php';

// Create a class that uses the Database trait
class TutorialEditor {
    use Database; // Use the Database trait

    public function getPdo() {
        return $this->connect(); // Establish and return the PDO connection
    }
}

// Instantiate the TutorialEditor class to get the PDO connection
$tutorialEditor = new TutorialEditor();
$pdo = $tutorialEditor->getPdo();

if (!$pdo) {
    die('Database connection is not established.');
}

// Fetch tutorial data for editing if a tutorial ID is provided
$tutorialId = $_GET['tutorial_id'] ?? null;
$tutorial = null;

if ($tutorialId) {
    $query = "SELECT * FROM tutorial WHERE tutorial_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tutorialId]);
    $tutorial = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Define a success message variable
$successMessage = ''; 

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['tutorial-title'];
    $description = $_POST['description'];
    $fileName = $_FILES['uploads']['name'];
    $fileTmpName = $_FILES['uploads']['tmp_name'];
    $uploadDir =  'uploads/'; // Absolute path to uploads folder

    // If a new file is uploaded, process it, otherwise keep the old file
    if (!empty($fileName)) {
        move_uploaded_file($fileTmpName, $uploadDir . $fileName);
        // Update the tutorial details with the new file
        $updateQuery = "UPDATE tutorial SET title = ?, description = ?, upload = ? WHERE tutorial_id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$title, $description, $fileName, $tutorialId]);
    } else {
        // No file uploaded, just update title and description
        $updateQuery = "UPDATE tutorial SET title = ?, description = ? WHERE tutorial_id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$title, $description, $tutorialId]);
    }

    // Set the success message
    $successMessage = "Tutorial updated successfully!";
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
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <!-- Add tutorial Section -->
    <main class="edit-tutorial-page">
        <section class="tutorial-form-container">
            <h1>Edit Tutorial</h1>

            <form action="#" method="POST" class="edit-tutorial-form" enctype="multipart/form-data">
                <label for="tutorial-no">Tutorial no</label>
                <input type="text" id="tutorial-no" name="tutorial-no" placeholder="Enter Tutorial number" value="<?php echo htmlspecialchars($tutorial['tutorial_id'] ?? ''); ?>" readonly>

                <label for="tutorial-title">Title</label>
                <input type="text" id="tutorial-title" name="tutorial-title" placeholder="Enter Tutorial title" value="<?php echo htmlspecialchars($tutorial['title'] ?? ''); ?>" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" value="<?php echo htmlspecialchars($tutorial['description'] ?? ''); ?>" required>

                <label for="uploads">Upload New File (if any)</label>
                <input type="file" id="uploads" name="uploads">

                <?php if (!empty($tutorial['upload'])): ?>
                    <div class="existing-file">
                        <p>Current file: <a href="uploads/<?php echo htmlspecialchars($tutorial['upload']); ?>" target="_blank"><?php echo htmlspecialchars($tutorial['upload']); ?></a></p>
                    </div>
                <?php endif; ?>

                <div class="form-controls">
                    
                    <button type="reset" class="cancel-button">Cancel</button>
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
            window.location.href = "classschedule.php"; // Redirect to another page
        }
    </script>
</body>
</html>
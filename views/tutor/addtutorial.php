<?php

session_start();


    require '../Database.php';

class Tutorial {
    use Database;

    public function addTutorial($tutorial_id, $title, $description, $upload) {
        $sql = "INSERT INTO tutorial (tutorial_id, class_id, title, description, upload) 
                VALUES (:tutorial_id, :class_id, :title, :description, :upload)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':tutorial_id' => $tutorial_id,
            ':class_id' => 1,
            ':title' => $title,
            ':description' => $description,
            ':upload' => $upload,
        ]);
    }
}

$successMessage = ''; // Variable for success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutorial_id = $_POST['tutorial-id'];
    $title = $_POST['tutorial-title'];
    $description = $_POST['description'];
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

    // Add the tutorial to the database
    $tutorial = new Tutorial();
    $tutorial->addTutorial($tutorial_id, $title, $description, $upload);

    // Set the success message after the tutorial is added
    $successMessage = "Tutorial added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/addtutorial.css">
    
</head>
<body>
    <header >
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <main class="add-tutorial-page">
        <section class="tutorial-form-container">
            <h1>Add Tutorial</h1>

            <form action="addtutorial.php" method="POST" enctype="multipart/form-data" class="add-tutorial-form">
                <label for="tutorial-id">Tutorial no</label>
                <input type="text" id="tutorial-id" name="tutorial-id" placeholder="Enter Tutorial number" required>

                <label for="tutorial-title">Title</label>
                <input type="text" id="tutorial-title" name="tutorial-title" placeholder="Enter Tutorial title" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" required>

                <label for="uploads">Uploads</label>
                <input type="file" id="uploads" name="uploads" required>

                <div class="form-controls">
                    <button type="reset" class="cancel-button">Cancel</button>
                    <button type="submit" class="add-button">Add</button>
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

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/grading.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">

</head>
<body>

<header>
m   <?php
    include '../header_tutor.php'
    ?>
    </header>

    <!-- Grading Form Section -->
    <main class="form-container">
        <h1>Grade Student Submissions</h1>
        <form action="grading.php" method="POST" class="grading-form">
                <label for="submission-id">Submission no</label>
                <input type="text" id="submission-id" name="submission-id" required>

                <label for="submission-title">Title </label>
                <input type="text" id="submission-title" name="submission-title"  required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" required>

                <label for="marks">Marks</label>
                <input type="text" id="marks" name="marks" placeholder="Enter Marks" required>

                <label for="grade">Grade</label>
                <input type="text" id="grade" name="grade" placeholder="Enter Grade" required>

                <div class="form-controls">
                    <button type="reset" class="cancel-button">Cancel</button>
                    <button type="submit" class="add-button">Grade</button>
                    
                </div>
            </form>
    </main>
  <!-- Footer Section -->
  <?php include '../footer.php'; ?>
</body>
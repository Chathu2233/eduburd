<?php
session_start();
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
    <link rel="stylesheet" href="../../assets/css/Tutor/editassignment.css">
</head>
<body>
    <header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <!-- Add assignment Section -->
    <main class="edit-assignment-page">
        <section class="assignment-form-container">
            <h1>Edit assignment</h1>

            <form action="#" method="POST" class="edit-assignment-form">
                <label for="assignment-no">Assignment no</label>
                <input type="text" id="assignment-no" name="assignment-no" placeholder="Enter assignment number" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description" required>

                <label for="uploads">Uploads</label>
                <input type="file" id="uploads" name="uploads" required>

                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" required>

                <div class="form-controls">
                    <button type="submit" class="add-button">Edit</button>
                    <button type="reset" class="cancel-button">Cancel</button>
                </div>
            </form>
        </section>
    </main>
    <?php include '../footer.php'; ?>
</body>

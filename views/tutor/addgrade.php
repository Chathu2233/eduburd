<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grade - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/addgrade.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>

    <header >
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <section class="add-grade-section">
        <h1>Add Grade</h1>
        <div class="form-container">
            <form action="/submit-grade" method="POST">
                <label for="grade">Grade</label>
                <input type="text" id="grade" name="grade" placeholder="Enter grade" required>

                <label for="qualifications">Qualifications</label>
                <input type="text" id="qualifications" name="qualifications" placeholder="Enter qualifications" required>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </section>

    <?php include '../footer.php'; ?>

</body>
</html>

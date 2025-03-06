<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/addsubject.css">
</head>
<body>

    <header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <section class="add-subject-section">
        <h1>Add Subject</h1>
        <div class="form-container">
            <form action="/submit-subject" method="POST">
                <label for="subject">Subjects</label>
                <input type="text" id="subject" name="subject" placeholder="Enter subject" required>

                <label for="qualifications">Qualifications</label>
                <input type="text" id="qualifications" name="qualifications" placeholder="Enter qualifications" required>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </section>
    <?php include '../footer.php'; ?>

</body>
</html>

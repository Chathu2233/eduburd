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
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/edit_time_slots.css">
</head>
<body>

<header>
<?php
    include '../header_tutor.php'
    ?>
    </header>

    <main class="main-content">

    <!-- Add New Slot -->
    <div class="add-slot">
            <h2>Edit Time Slot</h2>
            <form action="#" method="post" class="slot-form">
                <label for="day">Day of the Week:</label>
                <input type="text" id="day" name="day" placeholder="e.g., Monday" required>

                <label for="start-time">Start Time:</label>
                <input type="time" id="start-time" name="start-time" required>

                <label for="end-time">End Time:</label>
                <input type="time" id="end-time" name="end-time" required>

                <button type="submit" class="btn submit">Edit Time Slot</button>
            </form>
        </div>
    </div>
</main>


<?php include '../footer.php'; ?>

</body>
</html>
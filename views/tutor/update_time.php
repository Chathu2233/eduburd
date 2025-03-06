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
    <link rel="stylesheet" href="../../assets/css/Tutor/update_time.css">
</head>
<body>

<header>
<?php
    include '../header_tutor.php'
    ?>
    </header>

    <main class="main-content">
    <h1>Manage Time Slots</h1>
    <div class="dashboard">
        <!-- Existing Time Slots -->
        <div class="existing-slots">
            <h2>Existing Time Slots</h2>
            <div class="class-card">
                <span>Monday - 10:00 AM to 12:00 PM</span>
                <div class="actions">
                    <button class="btn edit"><a href="edit_time_slots.php">Edit</button>
                    <button class="btn delete">Delete</button>
                </div>
            </div>
            <div class="class-card">
                <span>Wednesday - 2:00 PM to 4:00 PM</span>
                <div class="actions">
                    <button class="btn edit"><a href="edit_time_slots.php">Edit</button>
                    <button class="btn delete">Delete</button>
                </div>
            </div>
        </div>

        <!-- Reserved Classes Section -->
        <div class="reserved-classes">
            <h2>Reserved Time Slots</h2>
            <div class="class-card">
                <span>Tuesday - 1:00 PM to 2:30 PM (Reserved by Farshad)</span>
            </div>
            <div class="class-card">
                <span>Thursday - 3:00 PM to 4:30 PM (Reserved by Ayathma)</span>
            </div>
        </div>

        <!-- Add New Slot -->
        <div class="add-slot">
            <h2>Add New Time Slot</h2>
            <form action="#" method="post" class="slot-form">
                <label for="day">Day of the Week:</label>
                <input type="text" id="day" name="day" placeholder="e.g., Monday" required>

                <label for="start-time">Start Time:</label>
                <input type="time" id="start-time" name="start-time" required>

                <label for="end-time">End Time:</label>
                <input type="time" id="end-time" name="end-time" required>

                <button type="submit" class="btn submit">Add Time Slot</button>
            </form>
        </div>
    </div>
</main>


<?php include '../footer.php'; ?>

</body>
</html>

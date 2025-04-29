<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Check if time_slot_id is provided in the URL
if (!isset($_GET['time_slot_id'])) {
    die("Time Slot ID not provided.");
}

$time_slot_id = $_GET['time_slot_id'];

// Fetch the existing time slot details
try {
    $stmt = $pdo->prepare("
        SELECT day, TIME_FORMAT(start_time, '%H:%i') AS start_time, TIME_FORMAT(end_time, '%H:%i') AS end_time 
        FROM time_slot 
        WHERE time_slot_id = :time_slot_id AND tutor_id = :tutor_id
    ");
    $stmt->execute([
        ':time_slot_id' => $time_slot_id,
        ':tutor_id' => $tutor_id,
    ]);
    $time_slot = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$time_slot) {
        die("Time slot not found or you do not have permission to edit this time slot.");
    }
} catch (PDOException $e) {
    die("Error fetching time slot: " . $e->getMessage());
}

// Handle form submission for updating the time slot
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = trim($_POST['day']);
    $start_time_hour = trim($_POST['start-time-hour']);
    $start_time_minute = trim($_POST['start-time-minute']);
    $end_time_hour = trim($_POST['end-time-hour']);
    $end_time_minute = trim($_POST['end-time-minute']);

    // Construct start_time and end_time
    $start_time = $start_time_hour . ':' . $start_time_minute . ':00';
    $end_time = $end_time_hour . ':' . $end_time_minute . ':00';

    // Validate input
    if (empty($day) || empty($start_time_hour) || empty($start_time_minute) || empty($end_time_hour) || empty($end_time_minute)) {
        die("All fields are required.");
    }

    try {
        // Update the time slot in the database
        $stmt = $pdo->prepare("
            UPDATE time_slot 
            SET day = :day, start_time = :start_time, end_time = :end_time 
            WHERE time_slot_id = :time_slot_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':day' => $day,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':time_slot_id' => $time_slot_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Set success message
        $_SESSION['success_message'] = "Time slot updated successfully!";

        // Redirect to avoid form resubmission
        header("Location: update_time.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating time slot: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Time Slot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/edit_time_slots.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<main class="main-content">
    <div class="add-slot">
        <h2>Edit Time Slot</h2>

        <!-- Display success message -->
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='success-message'>{$_SESSION['success_message']}</div>";
        unset($_SESSION['success_message']); // Clear the message after displaying it
    }
    ?>
        <form action="edit_time_slots.php?time_slot_id=<?= htmlspecialchars($time_slot_id) ?>" method="post" class="slot-form">
            <label for="day">Day of the Week:</label>
            <select id="day" name="day" required>
                <option value="" disabled>Select a Day</option>
                <option value="Monday" <?= $time_slot['day'] === 'Monday' ? 'selected' : '' ?>>Monday</option>
                <option value="Tuesday" <?= $time_slot['day'] === 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                <option value="Wednesday" <?= $time_slot['day'] === 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                <option value="Thursday" <?= $time_slot['day'] === 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                <option value="Friday" <?= $time_slot['day'] === 'Friday' ? 'selected' : '' ?>>Friday</option>
                <option value="Saturday" <?= $time_slot['day'] === 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                <option value="Sunday" <?= $time_slot['day'] === 'Sunday' ? 'selected' : '' ?>>Sunday</option>
            </select>

            <label for="start-time-hour">Start Time (24-hour format):</label>
            <div style="display: flex; gap: 10px;">
                <select id="start-time-hour" name="start-time-hour" required>
                    <option value="" disabled>Hour</option>
                    <?php for ($hour = 0; $hour < 24; $hour++): ?>
                        <option value="<?= sprintf('%02d', $hour) ?>" <?= sprintf('%02d', $hour) === substr($time_slot['start_time'], 0, 2) ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $hour) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select id="start-time-minute" name="start-time-minute" required>
                    <option value="" disabled>Minute</option>
                    <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
                        <option value="<?= sprintf('%02d', $minute) ?>" <?= sprintf('%02d', $minute) === substr($time_slot['start_time'], 3, 2) ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $minute) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <label for="end-time-hour">End Time (24-hour format):</label>
            <div style="display: flex; gap: 10px;">
                <select id="end-time-hour" name="end-time-hour" required>
                    <option value="" disabled>Hour</option>
                    <?php for ($hour = 0; $hour < 24; $hour++): ?>
                        <option value="<?= sprintf('%02d', $hour) ?>" <?= sprintf('%02d', $hour) === substr($time_slot['end_time'], 0, 2) ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $hour) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select id="end-time-minute" name="end-time-minute" required>
                    <option value="" disabled>Minute</option>
                    <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
                        <option value="<?= sprintf('%02d', $minute) ?>" <?= sprintf('%02d', $minute) === substr($time_slot['end_time'], 3, 2) ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $minute) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="btn submit">Update Time Slot</button>
        </form>
        <div class="back-button">
            <button class="styled-back-button" onclick="history.back()">← Back</button>
        </div>
    </div>


</main>

<?php include '../footer.php'; ?>

</body>
</html>
<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Handle form submission for adding a new time slot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day'], $_POST['start-time-hour'], $_POST['start-time-minute'], $_POST['end-time-hour'], $_POST['end-time-minute'])) {
    $day = trim($_POST['day']);
    $start_time_hour = trim($_POST['start-time-hour']);
    $start_time_minute = trim($_POST['start-time-minute']);
    $end_time_hour = trim($_POST['end-time-hour']);
    $end_time_minute = trim($_POST['end-time-minute']);

    $start_time = $start_time_hour . ':' . $start_time_minute;
    $end_time = $end_time_hour . ':' . $end_time_minute;

    // Validate input
    if (empty($day) || empty($start_time_hour) || empty($start_time_minute) || empty($end_time_hour) || empty($end_time_minute)) {
        die("All fields are required.");
    }

    try {
        // Insert the new time slot into the database
        $stmt = $pdo->prepare("
            INSERT INTO time_slot (tutor_id, day, start_time, end_time, status) 
            VALUES (:tutor_id, :day, :start_time, :end_time, 'available')
        ");
        $stmt->execute([
            ':tutor_id' => $tutor_id,
            ':day' => $day,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
        ]);

        // Set success message
        $_SESSION['success_message'] = "Time slot added successfully!";

        // Redirect to avoid form resubmission
        header("Location: update_time.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding time slot: " . $e->getMessage());
    }
}

// Handle deletion of a time slot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_time_slot_id'])) {
    $delete_time_slot_id = $_POST['delete_time_slot_id'];

    try {
        $stmt = $pdo->prepare("
            DELETE FROM time_slot 
            WHERE time_slot_id = :time_slot_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':time_slot_id' => $delete_time_slot_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Set success message
        $_SESSION['success_message'] = "Time slot deleted successfully!";

        // Redirect to avoid form resubmission
        header("Location: update_time.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting time slot: " . $e->getMessage());
    }
}

// Fetch existing time slots with status 'available'
try {
    $stmt = $pdo->prepare("
        SELECT time_slot_id, day, start_time, end_time 
        FROM time_slot 
        WHERE tutor_id = :tutor_id AND status = 'available'
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching available time slots: " . $e->getMessage());
}

// Fetch reserved time slots
try {
    $stmt = $pdo->prepare("
        SELECT time_slot_id, day, start_time, end_time 
        FROM time_slot 
        WHERE tutor_id = :tutor_id AND status = 'reserved'
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $reserved_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching reserved time slots: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/update_time.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<main class="main-content">
    <h1>Manage Time Slots</h1>

    <!-- Display success message -->
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='success-message'>{$_SESSION['success_message']}</div>";
        unset($_SESSION['success_message']);
    }
    ?>

    <div class="dashboard">
        <!-- Existing Time Slots -->
        <div class="existing-slots">
            <h2>Available Time Slots</h2>
            <?php if (!empty($time_slots)): ?>
                <?php foreach ($time_slots as $slot): ?>
                    <div class="class-card">
                        <span><?= htmlspecialchars($slot['day']) ?> - <?= htmlspecialchars($slot['start_time']) ?> to <?= htmlspecialchars($slot['end_time']) ?></span>
                        <div class="actions">
                            <button class="btn edit">
                                <a href="edit_time_slots.php?time_slot_id=<?= htmlspecialchars($slot['time_slot_id']) ?>">Edit</a>
                            </button>
                            <form action="update_time.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this time slot?');" style="display: inline;">
                                <input type="hidden" name="delete_time_slot_id" value="<?= htmlspecialchars($slot['time_slot_id']) ?>">
                                <button type="submit" class="btn delete">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No available time slots.</p>
            <?php endif; ?>
        </div>

        <!-- Reserved Classes Section -->
        <div class="reserved-classes">
            <h2>Reserved Time Slots</h2>
            <?php if (!empty($reserved_slots)): ?>
                <?php foreach ($reserved_slots as $slot): ?>
                    <div class="class-card">
                        <span><?= htmlspecialchars($slot['day']) ?> - <?= htmlspecialchars($slot['start_time']) ?> to <?= htmlspecialchars($slot['end_time']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reserved time slots available.</p>
            <?php endif; ?>
        </div>

        <!-- Add New Slot -->
        <div class="add-slot">
            <h2>Add New Time Slot</h2>
            <form action="update_time.php" method="post" class="slot-form">
                <label for="day">Day of the Week:</label>
                <select id="day" name="day" required>
                    <option value="" disabled selected>-- Select a Day --</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>

                <label for="start-time-hour">Start Time (24-hour format):</label>
                <div style="display: flex; gap: 10px;">
                    <select id="start-time-hour" name="start-time-hour" required>
                        <option value="" disabled selected>Hour</option>
                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                            <option value="<?= sprintf('%02d', $hour) ?>"><?= sprintf('%02d', $hour) ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="start-time-minute" name="start-time-minute" required>
                        <option value="" disabled selected>Minute</option>
                        <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
                            <option value="<?= sprintf('%02d', $minute) ?>"><?= sprintf('%02d', $minute) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <label for="end-time-hour">End Time (24-hour format):</label>
                <div style="display: flex; gap: 10px;">
                    <select id="end-time-hour" name="end-time-hour" required>
                        <option value="" disabled selected>Hour</option>
                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                            <option value="<?= sprintf('%02d', $hour) ?>"><?= sprintf('%02d', $hour) ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="end-time-minute" name="end-time-minute" required>
                        <option value="" disabled selected>Minute</option>
                        <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
                            <option value="<?= sprintf('%02d', $minute) ?>"><?= sprintf('%02d', $minute) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" class="btn submit">Add Time Slot</button>
            </form>
        </div>
    </div>
</main>

<?php include '../footer.php'; ?>

</body>
</html>

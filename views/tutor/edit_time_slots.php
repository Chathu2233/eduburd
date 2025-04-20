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

// Fetch all time slots for the tutor
try {
    $stmt = $pdo->prepare("
        SELECT time_slot_id, day, TIME_FORMAT(start_time, '%H:%i') AS start_time, TIME_FORMAT(end_time, '%H:%i') AS end_time 
        FROM time_slot 
        WHERE tutor_id = :tutor_id
    ");
    $stmt->execute([':tutor_id' => $tutor_id]);
    $time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching time slots: " . $e->getMessage());
}

// Handle form submission for updating the time slot
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = trim($_POST['day']);
    $start_time = trim($_POST['start-time']);
    $end_time = trim($_POST['end-time']);

    // Validate input
    if (empty($day) || empty($start_time) || empty($end_time)) {
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
        <form action="edit_time_slots.php?time_slot_id=<?= htmlspecialchars($time_slot_id) ?>" method="post" class="slot-form">
            <label for="day">Day of the Week:</label>
            <input type="text" id="day" name="day" value="<?= htmlspecialchars($time_slot['day']) ?>" required>

            <label for="start-time">Start Time:</label>
            <input type="time" id="start-time" name="start-time" value="<?= htmlspecialchars($time_slot['start_time']) ?>" required>

            <label for="end-time">End Time:</label>
            <input type="time" id="end-time" name="end-time" value="<?= htmlspecialchars($time_slot['end_time']) ?>" required>

            <button type="submit" class="btn submit">Update Time Slot</button>
        </form>
        <button class="back-button" onclick="history.back()">Back</button>
    </div>

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
                        <form action="update_time.php" method="POST" onsubmit="return confirmDelete();" style="display: inline;">
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
</main>

<?php include '../footer.php'; ?>

</body>
</html>
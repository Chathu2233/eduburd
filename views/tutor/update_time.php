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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day'], $_POST['start-time'], $_POST['end-time'])) {
    $day = trim($_POST['day']);
    $start_time = trim($_POST['start-time']);
    $end_time = trim($_POST['end-time']);

    // Validate input
    if (empty($day) || empty($start_time) || empty($end_time)) {
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/update_time.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<main class="main-content">
    <h1>Manage Time Slots</h1>
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

                <label for="start-time">Start Time:</label>
                <input type="time" id="start-time" name="start-time" required>

                <label for="end-time">End Time:</label>
                <input type="time" id="end-time" name="end-time" required>

                <button type="submit" class="btn submit">Add Time Slot</button>
            </form>
        </div>
                    
        <button class="back-button" onclick="history.back()">Back</button>
    </div>
</main>

<?php include '../footer.php'; ?>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this time slot?");
    }
</script>

</body>
</html>

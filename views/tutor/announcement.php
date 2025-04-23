<?php
session_start();
require '../db.php'; // Include the database connection

// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id'];

// Handle Add/Edit Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveAnnouncement'])) {
    $announcement_id = $_POST['announcement_id'] ?? null;
    $text = trim($_POST['announcementText']);
    $date = date('Y-m-d H:i:s'); // Current date and time

    if (empty($announcement_id)) {
        // Add new announcement
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tutor_announcement (grade_class_id, text, date)
                VALUES (:grade_class_id, :text, :date)
            ");
            $stmt->execute([
                ':grade_class_id' => $grade_class_id,
                ':text' => $text,
                ':date' => $date,
            ]);
            $_SESSION['success_message'] = "Announcement added successfully!";
        } catch (PDOException $e) {
            die("Error adding announcement: " . $e->getMessage());
        }
    } else {
        // Edit existing announcement
        try {
            $stmt = $pdo->prepare("
                UPDATE tutor_announcement
                SET text = :text, date = :date
                WHERE tutor_announcement_id = :announcement_id AND grade_class_id = :grade_class_id
            ");
            $stmt->execute([
                ':text' => $text,
                ':date' => $date,
                ':announcement_id' => $announcement_id,
                ':grade_class_id' => $grade_class_id,
            ]);
            $_SESSION['success_message'] = "Announcement updated successfully!";
        } catch (PDOException $e) {
            die("Error updating announcement: " . $e->getMessage());
        }
    }

    // Redirect to avoid form resubmission
    header("Location: announcement.php?grade_class_id=" . htmlspecialchars($grade_class_id));
    exit();
}

// Handle Delete Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAnnouncement'])) {
    $announcement_id = $_POST['announcement_id'];

    try {
        $stmt = $pdo->prepare("
            DELETE FROM tutor_announcement
            WHERE tutor_announcement_id = :announcement_id AND grade_class_id = :grade_class_id
        ");
        $stmt->execute([
            ':announcement_id' => $announcement_id,
            ':grade_class_id' => $grade_class_id,
        ]);
        $_SESSION['success_message'] = "Announcement deleted successfully!";
    } catch (PDOException $e) {
        die("Error deleting announcement: " . $e->getMessage());
    }

    // Redirect to avoid form resubmission
    header("Location: announcement.php?grade_class_id=" . htmlspecialchars($grade_class_id));
    exit();
}

// Fetch Announcements for the Selected Grade Class
try {
    $stmt = $pdo->prepare("
        SELECT tutor_announcement_id, text, date
        FROM tutor_announcement
        WHERE grade_class_id = :grade_class_id
        ORDER BY date DESC
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching announcements: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/announcements.css">
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<div class="dashboard-container">
    <?php include 'sidebar1.php'; ?> <!-- Include the sidebar -->
</div>

<div class="container">
    <!-- Main Content -->
    <div class="main-content">
        <h1>Manage Announcements</h1>
        <div class="button-container">
            <button class="add-button" onclick="toggleForm()">Add Announcement</button>
        </div>

        <!-- Add/Edit Announcement Form -->
        <div id="announcementForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Announcement</h2>
            <form action="" method="POST">
                <input type="hidden" id="announcementId" name="announcement_id">

                <label for="announcementText">Announcement Text:</label>
                <textarea id="announcementText" name="announcementText" rows="4" required></textarea>

                <button type="submit" name="saveAnnouncement">Save Announcement</button>
            </form>
        </div>

        <!-- Announcement History Table -->
        <div class="announcement-list">
            <h2>Announcement History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Text</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?= htmlspecialchars($announcement['text']) ?></td>
                            <td><?= htmlspecialchars($announcement['date']) ?></td>
                            <td>
                                <button onclick="editAnnouncement(<?= $announcement['tutor_announcement_id'] ?>, '<?= htmlspecialchars($announcement['text']) ?>')">Edit</button>
                                <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    <input type="hidden" name="announcement_id" value="<?= $announcement['tutor_announcement_id'] ?>">
                                    <button type="submit" name="deleteAnnouncement">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function toggleForm() {
        document.getElementById('announcementForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Announcement';
        document.getElementById('announcementId').value = '';
        document.getElementById('announcementText').value = '';
    }

    function editAnnouncement(id, text) {
        document.getElementById('announcementForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Announcement';
        document.getElementById('announcementId').value = id;
        document.getElementById('announcementText').value = text;
    }
</script>

</body>
</html>

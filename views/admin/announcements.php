<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit/Delete Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveAnnouncement'])) {
        $announcement_id = $_POST['announcement_id'] ?? null;
        $announcementText = $_POST['announcementText'];
        $audience = $_POST['audience'];
        $date = date('Y-m-d H:i:s');

        if (!empty($announcement_id)) {
            // Update existing announcement
            $query = "UPDATE announcements SET text = :text, audience = :audience, date = :date WHERE announcement_id = :announcement_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':text' => $announcementText,
                ':audience' => $audience,
                ':date' => $date,
                ':announcement_id' => $announcement_id
            ]);
        } else {
            // Insert new announcement
            $query = "INSERT INTO announcements (text, audience, date) VALUES (:text, :audience, :date)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':text' => $announcementText,
                ':audience' => $audience,
                ':date' => $date
            ]);
        }

        header('Location: ' . ROOT . '/views/admin/announcements.php');
        exit();
    }

    // Handle Delete Announcement
    if (isset($_POST['deleteAnnouncement'])) {
        $announcement_id = $_POST['announcement_id'];
        $query = "DELETE FROM announcements WHERE announcement_id = :announcement_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':announcement_id' => $announcement_id]);

        header('Location: ' . ROOT . '/views/admin/announcements.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/announcements.css">
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="announcements-container">
    <h1>Announcements</h1>
    
    <!-- Add Announcement Section -->
    <h2 id="formTitle">Add Announcement</h2>
    <form action="" method="POST">
        <input type="hidden" id="announcementId" name="announcement_id">
        <label for="announcementText">Announcement Text:</label>
        <textarea id="announcementText" name="announcementText" rows="4" required></textarea>
        
        <label for="audience">Select Audience:</label>
        <select id="audience" name="audience" required>
            <option value="students">Students</option>
            <option value="parents">Parents</option>
            <option value="teachers">Teachers</option>
            <option value="all">All</option>
        </select>
        
        <button type="submit" name="saveAnnouncement" class="add-button">Save Announcement</button>
    </form>
    
    <!-- Announcement History Table -->
    <h2>Announcement History</h2>
    <table>
        <thead>
            <tr>
                <th>Text</th>
                <th>Audience</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch announcements from the database
            $query = "SELECT * FROM announcements ORDER BY date DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                    <td>{$row['text']}</td>
                    <td>{$row['audience']}</td>
                    <td>{$row['date']}</td>
                    <td>
                        <button onclick=\"editAnnouncement({$row['announcement_id']}, '{$row['text']}', '{$row['audience']}')\">Edit</button>
                        <form action='' method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this announcement?');\">
                            <input type='hidden' name='announcement_id' value='{$row['announcement_id']}'>
                            <button type='submit' name='deleteAnnouncement'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../footer.php'; ?>

<script>
    function editAnnouncement(id, text, audience) {
        document.getElementById('announcementId').value = id;
        document.getElementById('announcementText').value = text;
        document.getElementById('audience').value = audience;
        document.getElementById('formTitle').innerText = 'Edit Announcement';
    }

    // Replace the current history entry with the dashboard URL
    history.replaceState(null, '', '<?php echo ROOT; ?>/views/admin/announcements.php');

    // Listen for the popstate event to detect when the user presses the back button
    window.addEventListener('popstate', function(event) {
        window.location.href = '<?php echo ROOT; ?>/views/admin/admindashboard.php';
    });
</script>

</body>
</html>

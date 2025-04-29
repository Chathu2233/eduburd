<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit/Delete Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveAnnouncement'])) {
        // Get form data
        $announcement_id = $_POST['announcement_id'] ?? null;
        $announcementText = $_POST['announcementText'];
        $audience = $_POST['audience'];
        $date = date('Y-m-d H:i:s');

        if (!empty($announcement_id)) {
            // Update existing announcement
            $query = "UPDATE admin_announcement SET text = :text, audience = :audience, date = :date WHERE admin_announcement_id = :announcement_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':text' => $announcementText,
                ':audience' => $audience,
                ':date' => $date,
                ':announcement_id' => $announcement_id
            ]);
        } else {
            // Insert new announcement
            $query = "INSERT INTO admin_announcement (text, audience, date, is_read) VALUES (:text, :audience, :date, 0)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':text' => $announcementText,
                ':audience' => $audience,
                ':date' => $date
            ]);
        }

        // Redirect to the same page after saving
        header('Location: ' . ROOT . '/views/admin/announcements.php');
        exit();
    }

    // Handle Delete Announcement
    if (isset($_POST['deleteAnnouncement'])) {
        $announcement_id = $_POST['announcement_id'];
        $query = "DELETE FROM admin_announcement WHERE admin_announcement_id = :announcement_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':announcement_id' => $announcement_id]);

        // Redirect to the same page after deletion
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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/announcements.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Manage Announcements</h1>

        <!-- Add Announcement Button -->
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

                <label for="audience">Select Audience:</label>
                <select id="audience" name="audience" required>
                    <option value="students">Students</option>
                    <option value="parents">Parents</option>
                    <option value="teachers">Teachers</option>
                    <option value="all">All</option>
                </select>

                <button type="submit" name="saveAnnouncement" class="save-btn">Save Announcement</button>
            </form>
        </div>

        <!-- Announcement History Table -->
        <div class="announcement-list">
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
                    $query = "SELECT * FROM admin_announcement ORDER BY date DESC";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['text']) . "</td>
                            <td>" . htmlspecialchars($row['audience']) . "</td>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>
                                <div class=\"action-buttons\">
                                <button class=\"edit-btn\" onclick=\"editAnnouncement({$row['admin_announcement_id']}, '" . htmlspecialchars($row['text'], ENT_QUOTES) . "', '{$row['audience']}')\">Edit</button>
                                <form action=\"\" method=\"POST\" onsubmit=\"return confirm('Are you sure you want to delete this announcement?');\">
                                    <input type=\"hidden\" name=\"announcement_id\" value=\"{$row['admin_announcement_id']}\">
                                    <button type=\"submit\" name=\"deleteAnnouncement\" class=\"delete-btn\">Delete</button>
                                </form>
                                </div>
                            </td>
                        </tr>";
                    }
                    ?>
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
        document.getElementById('announcementId').value = ''; // Ensure the ID is empty for new announcements
        document.getElementById('announcementText').value = '';
        document.getElementById('audience').value = 'students';
    }

    function editAnnouncement(id, text, audience) {
        // Show the form
        document.getElementById('announcementForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Announcement';

        // Populate the form fields
        document.getElementById('announcementId').value = id;
        document.getElementById('announcementText').value = text;
        document.getElementById('audience').value = audience;
    }
</script>

</body>
</html>

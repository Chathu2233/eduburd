<?php
session_start();
require_once '../constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/classhistory.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Class History</h2>
                <!-- Class History Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Joined</th>
                            <th>Time Left</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="class-history-table-body">
                        <tr>
                            <td>2024-11-29</td>
                            <td>10:00 AM</td>
                            <td>11:00 AM</td>
                            <td>1 hour</td>
                            <td><button class="delete-btn" onclick="deleteHistory(this)">Delete</button></td>
                        </tr>
                        <!-- Class history will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script>
        // Function to delete a class history entry
        function deleteHistory(button) {
            const row = button.closest('tr');
            if (confirm('Are you sure you want to delete this history?')) {
                row.remove();
            }
        }
    </script>
</body>
</html>
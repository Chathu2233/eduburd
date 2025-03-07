<?php
session_start();
require_once '../constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Classes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/upcomingclasses.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
    <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Upcoming Classes</h2>
                <!-- Upcoming Classes Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>20/11/2024</td>
                            <td>10:00 AM</td>
                            <td>Nouns</td>
                        </tr>
                        <tr>
                            <td>22/11/2024</td>
                            <td>02:00 PM</td>
                            <td>Verbs</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
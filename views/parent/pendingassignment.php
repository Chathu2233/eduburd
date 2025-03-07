<?php
session_start();
require_once '../constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Assignment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/pendingassignments.css">
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
                <h2>Pending Assignment</h2>
                <!-- Pending Homework Table -->
                <table class="homework-table">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Assignment 1</td>
                            <td class="status-pending">Pending</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Assignment 2</td>
                            <td class="status-done">Done</td>
                            <td><a href="path/to/homework/assignment2.pdf" target="_blank" class="btn-view">View</a></td>
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
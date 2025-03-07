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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/classschedule.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
<header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>
        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h1 class="page-title">Upcoming Classes</h1>
                <div class="table-wrapper">
                    <table class="class-table">
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
                                <td>Math Lesson: Calculus</td>
                            </tr>
                            <tr>
                                <td>22/11/2024</td>
                                <td>02:00 PM</td>
                                <td>Science Lesson: Chemistry</td>
                            </tr>
                            <tr>
                                <td>24/11/2024</td>
                                <td>11:00 AM</td>
                                <td>English Lesson: Writing Skills</td>
                            </tr>
                            <tr>
                                <td>26/11/2024</td>
                                <td>01:00 PM</td>
                                <td>Science Lesson: Optics</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

   <!-- Footer -->
   <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
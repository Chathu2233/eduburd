<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/view_submission.css">
</head>
<body>
    <!-- Header Section -->
    <header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>

    <!-- Page Title -->
    <h1 class="dashboard-title">Student Assignments</h1>

    <!-- Main Content -->
    <main>
        <!-- Assignments Section -->
        <section class="assignments-section">
            <h2>Assignment submissions</h2>
            <table class="assignments-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Assignment Title</th>
                        <th>Submission Date</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ayathma Amanethmi</td>
                        <td>Physics Chapter 3</td>
                        <td>2024-11-20</td>
                        <td>
                            <button class="view-btn">View</button>
                        </td>
                        <td>
                            <button class="view-btn"><a href="grading.php">Grading</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Farshad</td>
                        <td>Chem Chapter 3</td>
                        <td>2024-09-20</td>
                        <td>
                            <button class="view-btn">View</button>
                        </td>
                        <td>
                        <button class="view-btn"><a href="grading.php">Grading</button>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </section>

        <!-- Submission Details Section -->
        <section class="submission-section" id="submission-section">
            <h2 id="submission-title">Submission Title</h2>
            <div class="submission-content" id="submission-content">
                <!-- Submission content dynamically displayed here -->
            </div>
            <form class="mark-form">
                <label for="marks">Marks:</label>
                <input type="number" id="marks" name="marks" min="0" max="100" placeholder="Enter marks">
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4" placeholder="Add comments..."></textarea>
                <button type="submit" class="submit-btn">Submit Marks</button>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

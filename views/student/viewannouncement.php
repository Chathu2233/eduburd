<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd Announcements</title>
    <link rel="stylesheet" href="../../assets/css/student/viewannouncement.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Announcement Content -->
       
            <section class="announcements">
                <h1>General News and Announcements</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Announcements</th>
                            <th>Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Test announcement</td>
                            <td>36 minutes ago</td>
                        </tr>
                        <tr>
                            <td>Test announcement</td>
                            <td>12th July, 2024</td>
                        </tr>
                        <tr>
                            <td>Test announcement</td>
                            <td>12th July, 2024</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

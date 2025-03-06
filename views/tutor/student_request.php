<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/student_request.css">
<body>
<header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <!-- Student Requests Section -->
    <main>
        <section class="student-requests">
            <h2>📩 Student Requests</h2>
            <p>Manage incoming student requests by accepting or rejecting them.</p>

            <table class="request-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Grade</th>
                        <th>View Profile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ayathma </td>
                        <td>Grade 10</td>
                        <td><a href="view_student.php?id=1" class="view-profile">View Profile</a></td>
                        <td>
                            <button class="btn accept-btn">Accept</button>
                            <button class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Chathumini </td>
                        <td>Grade 8</td>
                        <td><a href="student_profile.php?id=2" class="view-profile">View Profile</a></td>
                        <td>
                            <button class="btn accept-btn">Accept</button>
                            <button class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Farshad</td>
                        <td>Grade 11</td>
                        <td><a href="student_profile.php?id=3" class="view-profile">View Profile</a></td>
                        <td>
                            <button class="btn accept-btn">Accept</button>
                            <button class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Sajidha</td>
                        <td>Grade 11</td>
                        <td><a href="student_profile.php?id=3" class="view-profile">View Profile</a></td>
                        <td>
                            <button class="btn accept-btn">Accept</button>
                            <button class="btn delete-btn">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
    <?php include '../footer.php'; ?>
</body>
</html>

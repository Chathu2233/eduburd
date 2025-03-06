<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/tutor_dashboard.css">
</head>
<body>
<header >
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <div class="container">
        
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Centered images"  width="50" height="50" style="margin-top: 30px; "  style="background-color: pink;">
        <ul>
        <div class="sidebar1">

            <li><a href="my_account.php"><i class="fas fa-user"></i>My Profile</a></li>
        </div>

        <div class="sidebar2">
            <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i>My Subjects</a></li>
        </div>

        <div class="sidebar3">

            <li><a href="student_request.php"><i class="fas fa-user-plus"></i> Student Requests</a></li>
        </div>

        <div class="sidebar3">
        <li><a href="announcement.php">Announcements</a></li>
        </div>
        <div class="sidebar5">
        <li><a href="../resourcelibrary.php">Resource Library</a></li>
        </div>

        <div class="sidebar6">
        <li><a href="editprofile.php">Edit Profile</a></li>
        </div>


        </ul>
    </div>
        <main class="dashboard">
            <section class="welcome">
                <h2>Welcome Back, Tutor!</h2>
                <p>Provide the best support to students.</p>
            </section>
            <section class="upcoming-classes">
                <h3>Upcoming Classes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Math Class</td>
                            <td>25th Nov</td>
                            <td>10:00 AM</td>
                            <td><button class="btn">Join Now</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <section class="student-requests">
                <h3>Student Requests</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Grade 10</td>
                            <td>
                                <button class="btn accept">Accept</button>
                                <button class="btn reject">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="class-requests">
            <h2>Class Requests</h2>
            <table>
                <tr>
                    <th>Time Slot</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Action</th>
                    <th></th>
                </tr>
                <tr>
                    <td>Time slot 1</td>
                    <td>Name</td>
                    <td>Grade</td>
                    <td><button class="btn accept">Accept</button> 
                    <button class="btn reject">Reject</button></td>
                </tr>
                
            </table>
        </section>

        <section class="view submissions">
            <h2>Recent Submissions</h2>
            <table>
                <tr>
                    <th>Student</th>
                    <th>Assignment No</th>
                    <th>Submissions</th>
                    <th>Grading</th>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>Assignment No</td>
                    <td><button class="btn accept"><a href = "view_submission.php">View</button> </td>
                    <td><button class="btn accept"><a href = "grading.php">Grade</button> </td>
                </tr>
                
            </table>
        </section>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>

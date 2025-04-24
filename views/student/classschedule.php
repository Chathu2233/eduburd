<?php
session_start();
require '../db.php'; // Include database connection

// Check if tutor_id and student_id are passed
if (!isset($_GET['tutor_id']) && !isset($_GET['student_id'])) {
    die("Missing tutor_id or student_id.");
}

$tutor_id = $_GET['tutor_id'];
$student_id = $_GET['student_id'];

try {
    // Fetch available time slots for the tutor
    $stmt_time_slots = $pdo->prepare("
        SELECT 
            ts.time_slot_id,
            ts.start_time,
            ts.end_time,
            ts.day,
            ts.status
        FROM time_slot ts
        WHERE ts.tutor_id = :tutor_id AND ts.status IN ('None')
    ");
    $stmt_time_slots->execute([':tutor_id' => $tutor_id]);
    $time_slots = $stmt_time_slots->fetchAll(PDO::FETCH_ASSOC);

    // Handle payment and insert into grade_class table
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_slot_id'], $_POST['course_id'])) {
        $time_slot_id = $_POST['time_slot_id'];
        $course_id = $_POST['course_id'];
        $day = $_POST['day'];
        $time = $_POST['time'];

        // Insert into grade_class table
        $stmt_insert = $pdo->prepare("
            INSERT INTO grade_class (tutor_id, student_id, course_id, grade_id, day, time, description)
            VALUES (:tutor_id, :student_id, :course_id, :grade_id, :day, :time, :description)
        ");
        $stmt_insert->execute([
            ':tutor_id' => $tutor_id,
            ':student_id' => $student_id,
            ':course_id' => $course_id,
            ':grade_id' => $grade_id, // Assuming this is the correct field
            ':day' => $day,
            ':time' => $time,
            ':description' => 'Class booked successfully.'
        ]);

        // Redirect to confirmation page or display success message
        header("Location: confirmation.php?success=1");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching time slots or processing payment: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Find a Tutor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/student/classschedule.css">
    <link rel="stylesheet" href="../../assets/css/header_student.css">
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

        <!-- Content Section -->
        <div class="content-wrapper">
            <!-- Page Breadcrumb -->
            <div class="breadcrumb">
                <p>Homepage &gt; Find a tutor &gt; Teacher name</p>
            </div>
            
            <div class="search-bar">
                <input type="text" placeholder="Search for a tutor...">
                <button class="search-btn">🔍</button>
            </div>

            <!-- Tutor Search Section -->
            <div class="tutor-search-section">
                <div class="tutor-details">
                    <div class="tutor-profile">
                        <div class="tutor-image"></div>
                        <div class="tutor-info">
                            <h2>Tharindu Senanayake</h2>
                            <p>Classes Taught: 200</p>
                            <p>Subjects: Physics</p>
                            <p>Price: LKR 3500 per hour</p>
                        </div>
                        <button class="request-btn">Cancel Teacher</button>
                        <button class="request-btn" onclick="window.location.href='feedback.php';">Send Feedback</button>
                    </div>
                </div>
            </div>

            <div class="announcement">
                <h2>Announcement</h2>
                <div class="announcement-content">
                    <p>
                        Dear Students,<br>
                        We are excited to share the updated class schedule for the upcoming week! 🎓<br><br>
                        📅 Schedule Highlights:<br>
                        Ensure to join the class 5 minutes early to avoid disruptions.<br>
                        For any scheduling conflicts or queries, feel free to reach out to us through the EduBurd Help Center.<br>
                        We look forward to your active participation! Let’s make learning fun and engaging.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <div class="bookclass">
                    <h2>Class Booking</h2>
                    <div class="booking-container">
                        <div class="booking-form">
                            <form method="POST" action="">
                                <label for="date">Select Date:</label>
                                <select id="date" name="date" required>
                                    <option value="">Select Day</option>
                                    <?php
                                    // Dynamically populate days from the time slots
                                    $days = array_unique(array_column($time_slots, 'day'));
                                    foreach ($days as $day) {
                                        echo "<option value='" . htmlspecialchars($day) . "'>" . htmlspecialchars($day) . "</option>";
                                    }
                                    ?>
                                </select>

                                <label for="time">Select Start Time:</label>
                                <select id="time" name="time" required>
                                    <option value="">Select Start Time</option>
                                </select>

                                <label for="end-time">End Time:</label>
                                <input type="text" id="end-time" name="end-time" readonly>

                                <label for="course_id">Course ID:</label>
                                <input type="text" id="course_id" name="course_id" required>

                                <input type="hidden" id="time_slot_id" name="time_slot_id">
                                <button type="submit">Proceed to Payment</button>
                            </form>
                        </div>

                        <div class="availability">
                            <h3>Teacher Available Hours</h3>
                            <div id="available-times">
                                <p>Please select a date and time to view available slots.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script>
        // JavaScript logic for handling time slots
    </script>
</body>
</html>

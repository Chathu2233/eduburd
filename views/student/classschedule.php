<?php
session_start();
require '../db.php'; // Include database connection


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

    // STEP 1: Get student_id and name for logged-in user
    $stmt1 = $pdo->prepare("
        SELECT 
            student.student_id, 
            user.first_name 
        FROM 
            student 
        JOIN 
            user ON student.user_id = user.user_id 
        WHERE 
            student.user_id = :user_id
    ");
    $stmt1->execute([':user_id' => $user_id]);
    $student = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student record not found for user_id: $user_id");
    }

    $student_id = $student['student_id'];

// Check if tutor_id is passed
if (!isset($_GET['tutor_id'])) {
    die("Missing tutor_id.");
}

$tutor_id = $_GET['tutor_id'];

// Fetch tutor details from the tutor table
$stmt_tutor = $pdo->prepare("
    SELECT 
        t.tutor_id,
        CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        t.years_of_experience,
        t.description,
        t.fee,
        u.profile_photo
    FROM tutor t
    JOIN user u ON t.user_id = u.user_id
    WHERE t.tutor_id = :tutor_id
");
$stmt_tutor->execute([':tutor_id' => $tutor_id]);
$tutor = $stmt_tutor->fetch(PDO::FETCH_ASSOC);

if (!$tutor) {
    die("Tutor not found.");
}

// Fetch courses taught by the tutor
$stmt_courses = $pdo->prepare("
    SELECT DISTINCT c.course_id, c.name
    FROM tutor_course tc
    JOIN course c ON tc.course_id = c.course_id
    WHERE tc.tutor_id = :tutor_id
");
$stmt_courses->execute([':tutor_id' => $tutor_id]);
$courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

// Fetch available time slots
$stmt_time_slots = $pdo->prepare("
    SELECT 
        ts.time_slot_id,
        ts.start_time,
        ts.end_time,
        ts.day,
        ts.status
    FROM time_slot ts
    WHERE ts.tutor_id = :tutor_id AND ts.status = 'Available'
");
$stmt_time_slots->execute([':tutor_id' => $tutor_id]);
$time_slots = $stmt_time_slots->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_slot_id'], $_POST['course_id'], $_POST['grade_id'])) {
    $time_slot_id = $_POST['time_slot_id'];
    $course_id = $_POST['course_id'];
    $grade_id = $_POST['grade_id'];

    // Insert booking request
    $stmt_insert = $pdo->prepare("
        INSERT INTO time_slot_request (time_slot_id, student_id, grade_id, course_id, status)
        VALUES (:time_slot_id, :student_id, :grade_id, :course_id, 'pending')
    ");
    $stmt_insert->execute([
        ':time_slot_id' => $time_slot_id,
        ':student_id' => $student_id,
        ':grade_id' => $grade_id,
        ':course_id' => $course_id
    ]);

    // Update time slot status
    $stmt_update = $pdo->prepare("
        UPDATE time_slot
        SET status = 'pending'
        WHERE time_slot_id = :time_slot_id
    ");
    $stmt_update->execute([':time_slot_id' => $time_slot_id]);

    header("Location: stu_dashboard.php");
    exit();
}

// Handle AJAX for fetching grades
if (isset($_GET['fetch_grades']) && isset($_GET['course_id']) && isset($_GET['tutor_id'])) {
    $course_id = $_GET['course_id'];
    $tutor_id = $_GET['tutor_id'];

    $stmt_grades = $pdo->prepare("
        SELECT 
            tcg.tutor_course_grade_id,
            tcg.tutor_course_id,
            tcg.grade_id,
            tcg.qualifications,
            g.grade
        FROM tutor_course_grade tcg
        INNER JOIN tutor_course tc ON tcg.tutor_course_id = tc.tutor_course_id
        INNER JOIN grade g ON tcg.grade_id = g.grade_id
        WHERE tc.course_id = :course_id
          AND tc.tutor_id = :tutor_id
    ");
    $stmt_grades->execute([
        ':course_id' => $course_id,
        ':tutor_id' => $tutor_id
    ]);

    $grades = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);

    // Send grades as option elements
    header('Content-Type: text/html');
    foreach ($grades as $grade) {
        echo "<option value='" . htmlspecialchars($grade['grade_id']) . "'>" . htmlspecialchars($grade['grade']) . "</option>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Class</title>
    <link rel="stylesheet" href="../../assets/css/student/classschedule.css">
</head>
<body>

<!-- Header -->
<header class="navbar">
    <?php include '../header_student.php'; ?>
</header>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <main class="dashboard">
        <section>
          

            <div class="tutor-search-section">
                <div class="tutor-details">
                    <div class="tutor-profile">
                        <div class="tutor-image">
                            <img src="../../assets/images/tutors/<?php echo htmlspecialchars($tutor['profile_photo']); ?>" alt="Tutor Photo">
                        </div>
                        <div class="tutor-info">
                            <h2><?php echo htmlspecialchars($tutor['tutor_name']); ?></h2>
                            <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?> years</p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>
                            <p><strong>Fee:</strong> LKR <?php echo htmlspecialchars($tutor['fee']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="bookclass">
                    <h2>Class Booking</h2>
                    <div class="booking-container">
                        <form method="POST" action="">
                            <!-- Course -->
                            <label for="course">Select Course:</label>
                            <select id="course" name="course_id" required onchange="fetchGrades(this.value)">
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                        <?php echo htmlspecialchars($course['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                          
                            <!-- Time Slot -->
                            <label for="time_slot">Select Time Slot:</label>
                            <select id="time_slot" name="time_slot_id" required>
                                <option value="">Select Time Slot</option>
                                <?php foreach ($time_slots as $slot): ?>
                                    <option value="<?php echo htmlspecialchars($slot['time_slot_id']); ?>">
                                        <?php echo htmlspecialchars($slot['day']) . " " . htmlspecialchars($slot['start_time']) . " to " . htmlspecialchars($slot['end_time']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit">Send Request</button>
                        </form>
                    </div>
                </div>
            </div>

        </section>
    </main>
</div>

<!-- Footer -->
<?php include '../footer.php'; ?>

<script>
function fetchGrades(courseId) {
    const gradeDropdown = document.getElementById('grade');
    gradeDropdown.innerHTML = "<option value=''>Loading...</option>";

    if (courseId) {
        fetch(`?fetch_grades=1&course_id=${courseId}&tutor_id=<?php echo $tutor_id; ?>`)
            .then(response => response.text())
            .then(data => {
                gradeDropdown.innerHTML = data;
            })
            .catch(error => {
                gradeDropdown.innerHTML = "<option value=''>Error loading grades</option>";
                console.error('Error fetching grades:', error);
            });
    } else {
        gradeDropdown.innerHTML = "<option value=''>Select Grade</option>";
    }
}
</script>

</body>
</html>

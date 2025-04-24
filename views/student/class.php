<?php
session_start();
require '../db.php'; // Include database connection

// Check if grade_class_id is passed
if (!isset($_GET['grade_class_id'])) {
    die("Missing grade_class_id.");
}

$grade_class_id = $_GET['grade_class_id'];

try {
    // Fetch class schedule based on grade_class_id
    $stmt = $pdo->prepare("
        SELECT 
            gc.grade_class_id,
            c.name AS course_name,
            gc.day,
            gc.time,
            CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
            t.tutor_id,
            t.years_of_experience,
            t.description,
            t.fee
        FROM grade_class gc
        JOIN course c ON gc.course_id = c.course_id
        JOIN tutor t ON gc.tutor_id = t.tutor_id
        JOIN user u ON t.user_id = u.user_id
        WHERE gc.grade_class_id = :grade_class_id
    ");
    $stmt->execute([':grade_class_id' => $grade_class_id]);
    $class_schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class_schedule) {
        die("Class schedule not found.");
    }

    // Fetch all class schedules for the student
    $stmt_all_classes = $pdo->prepare("
        SELECT 
            day,
            time,
            description
        FROM grade_class
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt_all_classes->execute([':grade_class_id' => $grade_class_id]);
    $all_classes = $stmt_all_classes->fetchAll(PDO::FETCH_ASSOC);

    // Fetch assignments and their submission details for the grade_class_id
    $stmt_assignments = $pdo->prepare("
        SELECT 
            a.assignment_id, 
            a.title, 
            a.description, 
            a.deadline,
            a.file,
            s.assignment_submission_id,
            s.created_at,
            s.file AS submission_file,
            s.comment,
            s.grade,
            s.marks
        FROM assignment a
        LEFT JOIN assignment_submission s 
            ON a.assignment_id = s.assignment_id
        WHERE a.grade_class_id = :grade_class_id
        ORDER BY a.deadline ASC
    ");
    $stmt_assignments->execute([':grade_class_id' => $grade_class_id]);
    $assignments = $stmt_assignments->fetchAll(PDO::FETCH_ASSOC);

    // Sort assignments: Unsubmitted ones first
    usort($assignments, function ($a, $b) {
        return $a['is_submitted'] - $b['is_submitted'];
    });

    // Fetch announcements for the grade_class_id
    $stmt_announcements = $pdo->prepare("
        SELECT 
            tutor_announcement_id, 
            text, 
            date 
        FROM tutor_announcement 
        WHERE grade_class_id = :grade_class_id
        ORDER BY date DESC
    ");
    $stmt_announcements->execute([':grade_class_id' => $grade_class_id]);
    $announcements = $stmt_announcements->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Class Schedule</title>
    <link rel="stylesheet" href="../../assets/css/student/class.css">
    <link rel="stylesheet" href="../../assets/css/header_student.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>

    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
    

        <!-- Content Section -->
        <div class="content-wrapper">
            <!-- Page Breadcrumb -->
            <div class="breadcrumb">
                <p>Homepage &gt; Find a tutor &gt; <?php echo htmlspecialchars($class_schedule['tutor_name']); ?></p>
            </div>
            
            <div class="search-bar">
                <input type="text" placeholder="Search for a tutor...">
                <button class="search-btn">🔍</button>
            </div>

            <!-- Tutor Details Section -->
            <div class="tutor-details">
                <div class="tutor-profile">
                    <div class="tutor-image"></div>
                    <div class="tutor-info">
                        <h2><?php echo htmlspecialchars($class_schedule['tutor_name']); ?></h2>
                        <p>Years of Experience: <?php echo htmlspecialchars($class_schedule['years_of_experience']); ?></p>
                        <p>Subjects: <?php echo htmlspecialchars($class_schedule['course_name']); ?></p>
                        <p>Price: LKR <?php echo htmlspecialchars($class_schedule['fee']); ?> per hour</p>
                        <p>Description: <?php echo htmlspecialchars($class_schedule['description']); ?></p>
                    </div>
                    <button class="request-btn" onclick="window.location.href='feedback.php?grade_class_id=<?=htmlspecialchars($class_schedule['grade_class_id']) ?>';">Send Feedback</button>
                </div>
            </div>

            <!-- Class Schedule Section -->
            <div class="content-section">
                <div class="tabs">
                    <button class="tab-button active" onclick="openTab(event, 'class-schedule')">Class Schedule</button>
                    <button class="tab-button" onclick="openTab(event, 'join-class')">Join Class</button>
                    <button class="tab-button" onclick="openTab(event, 'tutorials')">Assignments</button>
                    <button class="tab-button" onclick="openTab(event, 'announcements')">Announcements</button>
                </div>

                <div id="class-schedule" class="tab-content active-content">
                    <h2>Class Schedule</h2>
                    <?php if (empty($all_classes)): ?>
                        <p>No class schedules available.</p>
                    <?php else: ?>
                        <ul class="class-schedule-list">
                            <?php foreach ($all_classes as $class): ?>
                                <li>
                                    <strong>Day:</strong> <?php echo htmlspecialchars($class['day']); ?> <br>
                                    <strong>Time:</strong> <?php echo htmlspecialchars($class['time']); ?> <br>
                                    <strong>Description:</strong> <?php echo htmlspecialchars($class['description']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div id="join-class" class="tab-content">
                    <h2>Join Class</h2>
                    <p>2024-10-10<button>Join Class</button></p>
                </div>

                <div id="tutorials" class="tab-content">
    <h2>Assignments</h2>
    <?php if (empty($assignments)): ?>
        <p>No assignments available for this class.</p>
    <?php else: ?>
        <ul class="assignment-list">
            <?php foreach ($assignments as $assignment): ?>
                <li class="assignment-item">
                    <div class="assignment-header">
                        <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                        <div class="assignment-actions">
                            <span class="status <?php echo !empty($assignment['assignment_submission_id']) ? 'submitted' : (date('Y-m-d') > $assignment['deadline'] ? 'closed' : ''); ?>">
                                <?php if (!empty($assignment['assignment_submission_id'])): ?>
                                    ✔ Submitted
                                    <?php if (!empty($assignment['grade'])): ?>
                                        (Grade: <?php echo htmlspecialchars($assignment['grade']); ?>)
                                    <?php endif; ?>
                                <?php elseif (date('Y-m-d') > $assignment['deadline']): ?>
                                    Submission Closed
                                <?php endif; ?>
                            </span>
                            <?php if (empty($assignment['assignment_submission_id']) && date('Y-m-d') <= $assignment['deadline']): ?>
                                <button class="submit-btn" onclick="window.location.href='submission.php?assignment_id=<?php echo $assignment['assignment_id']; ?>'">Submit</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p><?php echo htmlspecialchars($assignment['description']); ?></p>
                    <p><strong>Deadline:</strong> <?php echo htmlspecialchars($assignment['deadline']); ?></p>
                    <?php if (!empty($assignment['comment'])): ?>
                        <p><strong>Comment:</strong> <?php echo htmlspecialchars($assignment['comment']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($assignment['submission_file'])): ?>
                        <p><strong>Submitted File:</strong> <a href="uploads/<?php echo htmlspecialchars($assignment['submission_file']); ?>" download>Download</a></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

                <!-- New Announcement Tab -->
                <div id="announcements" class="tab-content">
                    <h2>Announcements</h2>
                    <?php if (empty($announcements)): ?>
                        <p>No announcements available for this class.</p>
                    <?php else: ?>
                        <ul class="announcement-list">
                            <?php foreach ($announcements as $announcement): ?>
                                <li class="announcement-item">
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($announcement['date']); ?></p>
                                    <p><?php echo nl2br(htmlspecialchars($announcement['text'])); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script>
        // Function to handle tab switching
        function openTab(event, tabId) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active-content'));

            // Remove 'active' class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show the selected tab content and set the button as active
            document.getElementById(tabId).classList.add('active-content');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>

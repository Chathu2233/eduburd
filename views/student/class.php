<?php 
session_start();
require '../db.php'; // Include database connection

// Check if grade_class_id is passed
if (!isset($_GET['grade_class_id'])) {
    die("Missing grade_class_id.");
}

$grade_class_id = $_GET['grade_class_id'];

try {
    // Fetch class schedule and tutor details, including the link
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
            t.fee,
            t.link, -- Fetch the link column
            u.profile_photo
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

    // Resolve the profile photo path
    $profilePhotoPath = '../../' . $class_schedule['profile_photo'];
    if (!file_exists($profilePhotoPath) || empty($class_schedule['profile_photo'])) {
        $profilePhotoPath = '../../assets/images/studentpropic.png'; // Fallback to default photo
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

    // Add 'is_submitted' key to each assignment
    foreach ($assignments as &$assignment) {
        $assignment['is_submitted'] = !empty($assignment['assignment_submission_id']) ? 1 : 0;
    }
    unset($assignment); // Break reference after foreach

    // Sort assignments: Unsubmitted ones first
    usort($assignments, function ($a, $b) {
        return $a['is_submitted'] - $b['is_submitted'];
    });

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Check if payment exists for this class
$stmt_payment = $pdo->prepare("
    SELECT * FROM payment 
    WHERE grade_class_id = :grade_class_id
");
$stmt_payment->execute([':grade_class_id' => $grade_class_id]);
$payment_exists = $stmt_payment->fetch(PDO::FETCH_ASSOC) ? true : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/class.css">
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

        <!-- Parent Content -->
        <main class="dashboard">
            <section>
               <!-- Tutor Details Section -->
<div class="tutor-details-card">
    <div class="card">
        <div class="card-header">
            <div class="tutor-image">
                <img src="<?= htmlspecialchars($profilePhotoPath); ?>" alt="Tutor Profile">
            </div>
        </div>
        <div class="card-body">
            <h2 class="tutor-name"><?= htmlspecialchars($class_schedule['tutor_name']); ?></h2>
            <div class="tutor-info">
                <p><strong>Years of Experience:</strong> <?= htmlspecialchars($class_schedule['years_of_experience']); ?> years</p>
                <p><strong>Subjects:</strong> <?= htmlspecialchars($class_schedule['course_name']); ?></p>
                <p><strong>Price:</strong> LKR <?= htmlspecialchars($class_schedule['fee']); ?> per hour</p>
                <p><strong>Description:</strong></p>
                <p class="description-text"><?= nl2br(htmlspecialchars($class_schedule['description'])); ?></p>
            </div>
        </div>
        <div class="card-footer">
            <button class="request-btn" onclick="window.location.href='feedback.php?grade_class_id=<?= htmlspecialchars($class_schedule['grade_class_id']); ?>';">Send Feedback</button>
            <!-- filepath: c:\xampp\htdocs\eduburd\views\student\class.php -->
            <?php if (!$payment_exists): ?>
    <button class="request-btn">
        <a href="checkout.php?tutor_name=<?= urlencode($class_schedule['tutor_name']); ?>&fee=<?= urlencode($class_schedule['fee']); ?>&grade_class_id=<?= urlencode($class_schedule['grade_class_id']); ?>">Pay now</a>
    </button>
<?php else: ?>
    <button class="request-btn" style="background-color: gray; cursor: not-allowed;" disabled>
        Already Paid
    </button>
<?php endif; ?>

        </div>
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

                <!-- filepath: c:\xampp\htdocs\eduburd\views\student\class.php -->
<div id="class-schedule" class="tab-content active-content">
    <h2>Class Schedule</h2>
    <?php if (empty($all_classes)): ?>
        <p class="no-schedule-message">No class schedules available.</p>
    <?php else: ?>
        <ul class="class-schedule-list">
            <?php foreach ($all_classes as $class): ?>
                <li class="class-schedule-item">
                    <div class="schedule-day-time">
                        <span class="schedule-day"><strong>Day:</strong> <?= htmlspecialchars($class['day']); ?></span>
                        <span class="schedule-time"><strong>Time:</strong> <?= htmlspecialchars($class['time']); ?></span>
                    </div>
                    <div class="schedule-description">
                        <strong>Description:</strong> <?= htmlspecialchars($class['description']); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
                    <div id="join-class" class="tab-content">
                        <h2>Join Class</h2>
                        <?php if (!empty($class_schedule['link'])): ?>
                            <p>Click the button below to join the class:</p>
                            <a href="<?= htmlspecialchars($class_schedule['link']); ?>" target="_blank" class="join-now-btn">
                                Join Now
                            </a>
                        <?php else: ?>
                            <p>No class link available for this tutor.</p>
                        <?php endif; ?>
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
                                            <h3><?= htmlspecialchars($assignment['title']); ?></h3>
                                            <div class="assignment-actions">
                                                <span class="status <?= !empty($assignment['assignment_submission_id']) ? 'submitted' : (date('Y-m-d') > $assignment['deadline'] ? 'closed' : ''); ?>">
                                                    <?php if (!empty($assignment['assignment_submission_id'])): ?>
                                                        ✔ Submitted
                                                        <?php if (!empty($assignment['grade'])): ?>
                                                            (Grade: <?= htmlspecialchars($assignment['grade']); ?>)
                                                        <?php endif; ?>
                                                    <?php elseif (date('Y-m-d') > $assignment['deadline']): ?>
                                                        Submission Closed
                                                    <?php endif; ?>
                                                </span>
                                                <?php if (empty($assignment['assignment_submission_id']) && date('Y-m-d') <= $assignment['deadline']): ?>
                                                    <button class="submit-btn" onclick="window.location.href='submission.php?assignment_id=<?= $assignment['assignment_id']; ?>'">Submit</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <p><?= htmlspecialchars($assignment['description']); ?></p>
                                        <p><strong>Deadline:</strong> <?= htmlspecialchars($assignment['deadline']); ?></p>
                                        <?php if (!empty($assignment['comment'])): ?>
                                            <p><strong>Comment:</strong> <?= htmlspecialchars($assignment['comment']); ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($assignment['submission_file'])): ?>
                                            <p><strong>Submitted File:</strong> <a href="uploads/<?= htmlspecialchars($assignment['submission_file']); ?>" download>Download</a></p>
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
                                        <p><strong>Date:</strong> <?= htmlspecialchars($announcement['date']); ?></p>
                                        <p><?= nl2br(htmlspecialchars($announcement['text'])); ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                </div>
            </section>
        </main>
                                </div>

        <!-- Footer -->
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
    </div>
</body>
</html>
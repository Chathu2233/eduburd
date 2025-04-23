<?php

 session_start();
// Include the database connection
require '../db.php';

// Set the correct timezone
date_default_timezone_set('Asia/Colombo'); // Replace with your timezone


// Check if grade_class_id is provided in the URL
if (!isset($_GET['grade_class_id'])) {
    die("Class ID not provided.");
}

$grade_class_id = $_GET['grade_class_id'];

// Fetch tutorials for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT tutorial_id, title, description, upload
        FROM tutorial
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $tutorials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tutorials: " . $e->getMessage());
}
// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tutorial_id'])) {
    $delete_tutorial_id = $_POST['delete_tutorial_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM tutorial WHERE tutorial_id = :tutorial_id");
        $stmt->bindParam(':tutorial_id', $delete_tutorial_id, PDO::PARAM_INT);
        $stmt->execute();

        // Store the success message in a session
        $_SESSION['success_message'] = "Tutorial deleted successfully!";
        // Redirect to avoid form resubmission
        header("Location: classschedule.php?grade_class_id=" . htmlspecialchars($grade_class_id));
        exit();
    } catch (PDOException $e) {
        echo '<p style="color: red; text-align: center;">Error deleting tutorial: ' . $e->getMessage() . '</p>';
    }
}

// Fetch assignments for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT assignment_id, title, description, deadline, file
        FROM assignment
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching assignments: " . $e->getMessage());
}

// Handle delete action for assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_assignment_id'])) {
    $delete_assignment_id = $_POST['delete_assignment_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM assignment WHERE assignment_id = :assignment_id");
        $stmt->bindParam(':assignment_id', $delete_assignment_id, PDO::PARAM_INT);
        $stmt->execute();

        // Store the success message in a session
        $_SESSION['success_message'] = "Assignment deleted successfully!";
        // Redirect to avoid form resubmission
        header("Location: classschedule.php?grade_class_id=" . htmlspecialchars($grade_class_id));
        exit();
    } catch (PDOException $e) {
        echo '<p style="color: red; text-align: center;">Error deleting assignment: ' . $e->getMessage() . '</p>';
    }
}

// Fetch assignment submissions for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.assignment_id, 
            a.title, 
            a.deadline, 
            s.marks 
        FROM 
            assignment a
        LEFT JOIN 
            assignment_submission s ON a.assignment_id = s.assignment_id
        WHERE 
            a.grade_class_id = :grade_class_id
        ORDER BY 
            a.assignment_id ASC
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $assignment_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching assignment submissions: " . $e->getMessage());
}

try {
    $today = new DateTime();
    $endOfMonth = new DateTime('last day of this month');
    $datesInMonth = [];

    // Build date-to-weekday mapping
    while ($today <= $endOfMonth) {
        $datesInMonth[] = [
            'date' => $today->format('Y-m-d'),
            'day_name' => $today->format('l'), // 'Monday', 'Tuesday', etc.
        ];
        $today->modify('+1 day');
    }

    // Fetch classes for the specific grade_class_id
    $stmt = $pdo->prepare("
        SELECT 
            gc.grade_class_id, 
            g.grade AS grade_name, 
            c.name AS course_name, 
            gc.day, 
            gc.time,
            t.link AS zoom_link
        FROM 
            grade_class gc
        JOIN 
            grade g ON gc.grade_id = g.grade_id
        JOIN 
            course c ON gc.course_id = c.course_id
        JOIN 
            tutor t ON gc.tutor_id = t.tutor_id
        WHERE 
            gc.grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $allClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Match real calendar dates with fixed class days
    $upcoming_classes = [];
    $currentDateTime = new DateTime();

    foreach ($datesInMonth as $dateInfo) {
        foreach ($allClasses as $class) {
            if (strcasecmp($class['day'], $dateInfo['day_name']) == 0) {
                // Combine date and class time
                $classDateTime = new DateTime($dateInfo['date'] . ' ' . $class['time']);
                if ($classDateTime >= $currentDateTime) {
                    $upcoming_classes[] = [
                        'class_date' => $dateInfo['date'],
                        'day' => $dateInfo['day_name'],
                        'time' => $class['time'],
                        'course_name' => $class['course_name'],
                        'grade_name' => $class['grade_name'],
                        'zoom_link' => $class['zoom_link']
                    ];
                }
                if (count($upcoming_classes) >= 4) break 2; // Stop at 4 classes
            }
        }
    }
} catch (PDOException $e) {
    die("Error fetching classes: " . $e->getMessage());
}

// Fetch student details for the selected grade_class_id
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name AS student_first_name,
            u.last_name AS student_last_name,
            u.email AS student_email,
            u.profile_photo AS student_photo,
            g.grade AS student_grade
        FROM 
            grade_class gc
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        JOIN 
            grade g ON gc.grade_id = g.grade_id
        WHERE 
            gc.grade_class_id = :grade_class_id
    ");
    $stmt->bindParam(':grade_class_id', $grade_class_id, PDO::PARAM_INT);
    $stmt->execute();
    $student_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student_details) {
        die("No student details found for the given Grade Class ID.");
    }
} catch (PDOException $e) {
    die("Error fetching student details: " . $e->getMessage());
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
  
    <link rel="stylesheet" href="../../assets/css/Tutor/classschedule.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/student_progress.css">
  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
</head>
<body>

    <!-- Header Section -->

    <header>
        <<?php
    include '../header_tutor.php'
    ?>
    </header>



 <!-- Content Section -->
 <div class="content-wrapper">

    <div class="search-bar">
        <input type="text" placeholder="Search for a class...">
        <button class="search-btn">🔍</button>
    </div>
  
        
    <div class="dashboard-container">
        <div class="sidebar">
        <a href="classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>">
        <img src="../../assets/images/dashboard.png" alt="Dashboard" width="50" height="50" style="margin-top: 30px;">
    </a>
        <ul>
        <div class="sidebar1">
            <li><a href="my_account.php"><i class="fas fa-user"></i> My Profile</a></li>
        </div>
        <div class="sidebar3">
        <li><a href="contact_parent.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-user-plus"></i> Contact Parent</a></li>        </div>

        <div class="sidebar3">
            <li><a href="view_student.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-edit"></i> Student Profile </a></li>
        </div>

        <div class="sidebar4">
            <li><a href="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-edit"></i> Parent Comments </a></li>
        </div>
        <div class="sidebar5">
            <li><a href="announcement.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        </div>

        <div class="sidebar6">
            <li><a href="../resourcelibrary.php"><i class="fas fa-credit-card"></i> Resource Library</a></li>
        </div>


        </ul>
    </div>

        <div class="content-section">
            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'join-class')">Join class</button>
                <button class="tab-button" onclick="openTab(event, 'tutorials')">Tutorials</button>
                <button class="tab-button" onclick="openTab(event, 'assignments')">Assignments</button>
                <button class="tab-button" onclick="openTab(event, 'view-progress')">View progress</button>
            </div>
            
            <div id="join-class" class="tab-content active-content">
    <div class="class-schedule">
        <h3>Upcoming Classes</h3>
        <?php if (!empty($upcoming_classes)): ?>
            <?php foreach ($upcoming_classes as $class): ?>
                <div class="class-item">
                    <span>
                        <?= htmlspecialchars($class['class_date']) ?> (<?= htmlspecialchars($class['day']) ?>),
                        <?= htmlspecialchars($class['time']) ?> -
                        <?= htmlspecialchars($class['course_name']) ?> (Grade <?= htmlspecialchars($class['grade_name']) ?>)
                    </span>
                    <?php
                    // Combine class date and time
                    $classDateTime = new DateTime($class['class_date'] . ' ' . $class['time']);
                    $currentDateTime = new DateTime();

                    // Calculate the time difference in seconds
                    $timeDifference = $classDateTime->getTimestamp() - $currentDateTime->getTimestamp();

                    // Debugging output
                    // echo "Class Time: " . $classDateTime->format('Y-m-d H:i:s') . "<br>";
                    // echo "Current Time: " . $currentDateTime->format('Y-m-d H:i:s') . "<br>";
                    // echo "Time Difference: " . $timeDifference . " seconds<br>";

                    // Enable the button if the class starts in 5 minutes or less
                    $isJoinEnabled = $timeDifference <= 300 && $timeDifference > 0;
                    ?>
                    <a href="<?= htmlspecialchars($class['zoom_link']) ?>" target="_blank">
                        <button class="join-now" <?= $isJoinEnabled ? '' : 'disabled' ?>>Join Now</button>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No upcoming classes available.</p>
        <?php endif; ?>
    </div>
</div>



            <div id="tutorials" class="tab-content">
                <h3>Tutorials</h3>
                <div class="tutorial-controls">
                <button><a href="addtutorial.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" class="addtutorial">Add Tutorial</a></button>
                </div>
                    
                <?php if (!empty($tutorials)): ?>
                        <?php foreach ($tutorials as $tutorial): ?>
                            <div class="tutorial-item">
                                <span><?php echo htmlspecialchars($tutorial['title']); ?></span>
                                <form action="edit_tutorial.php" method="GET" style="display: inline;">
                                    <input type="hidden" name="tutorial_id" value="<?php echo htmlspecialchars($tutorial['tutorial_id']); ?>">
                                    <button type="submit" class="edit">Edit</button>
                                </form>
                                <form action="" method="POST" onsubmit="return confirmDelete();" style="display: inline;">
                                    <input type="hidden" name="delete_tutorial_id" value="<?php echo htmlspecialchars($tutorial['tutorial_id']); ?>">
                                    <button type="submit" class="delete">Delete</button>
                        </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tutorials available.</p>
                    <?php endif; ?>
                
               
            </div>

            <div id="assignments" class="tab-content">
    <h2>Assignments</h2>
    <div>
        <div class="assignment-controls">
            <button><a href="addassignment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>" class="addassignment">Add Assignment</a></button>
        </div>
        <?php if (!empty($assignments)): ?>
            <?php foreach ($assignments as $assignment): ?>
                <div class="assignment-item">
                    <h3><?= htmlspecialchars($assignment['title']) ?></h3>
                    <p><strong>Deadline:</strong> <?= htmlspecialchars($assignment['deadline']) ?></p>
                    <a href="<?= htmlspecialchars($assignment['file']) ?>" download>
                        <button class="download-button">Download</button>
                    </a>
                    <form action="editassignment.php" method="GET" style="display: inline;">
                        <input type="hidden" name="assignment_id" value="<?= htmlspecialchars($assignment['assignment_id']) ?>">
                        <button type="submit" class="edit">Edit</button>
                    </form>
                    <form action="" method="POST" onsubmit="return confirmDelete();" style="display: inline;">
                        <input type="hidden" name="delete_assignment_id" value="<?= htmlspecialchars($assignment['assignment_id']) ?>">
                        <button type="submit" class="delete">Delete</button>
                    </form>
                    <form action="view_submission.php" method="GET" style="display: inline;">
                        <input type="hidden" name="assignment_id" value="<?= htmlspecialchars($assignment['assignment_id']) ?>">
                        <button type="submit" class="view-submission">Submission</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No assignments available.</p>
        <?php endif; ?>
    </div>
</div>

            
    <!-- View Progress Section -->
    <div id="view-progress" class="tab-content">
        <!-- Student Info -->
        <section class="student-info">
            <img src="<?= htmlspecialchars($student_details['student_photo']) ?>" alt="Student Profile" class="student-avatar">
            <div class="student-details">
                <h1><?= htmlspecialchars($student_details['student_first_name'] . ' ' . $student_details['student_last_name']) ?></h1>
                <p><strong>Grade:</strong> <?= htmlspecialchars($student_details['student_grade']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student_details['student_email']) ?></p>
            </div>
        </section>

        <!-- Progress Table -->
        <section class="progress-table">
            <h2>Progress Table</h2>
            <table>
                <thead>
                    <tr>
                        <th>Assignment No</th>
                        <th>Title</th>
                        <th>Marks</th>
                        <th>Progress (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $previous_marks = null;
                    foreach ($assignment_submissions as $index => $submission):
                        $progress = "---";
                        if ($previous_marks !== null && $submission['marks'] !== null) {
                            $progress = number_format((($submission['marks'] - $previous_marks) / $previous_marks) * 100, 2) . "%";
                        }
                        $previous_marks = $submission['marks'];
                    ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($submission['title']) ?></td>
                            <td><?= $submission['marks'] !== null ? htmlspecialchars($submission['marks']) : "N/A" ?></td>
                            <td><?= $progress ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Progress Chart -->
        <section class="progress-chart">
            <h2>Progress Chart</h2>
            <div class="chart-placeholder">
                <?php foreach ($assignment_submissions as $index => $submission): ?>
                    <?php if ($submission['marks'] !== null): ?>
                        <div class="bar" style="width: <?= htmlspecialchars($submission['marks']) ?>%; background-color: <?= $index % 2 === 0 ? '#4caf50' : '#2196f3' ?>;">
                            Assignment <?= $index + 1 ?>: <?= htmlspecialchars($submission['marks']) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
                    
        </div>
 

</div>


   
</div>
</body>
</html>

    </body>

    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="modal" id="successModal" style="display: flex;">
        <div class="modal-content">
            <h2><?= htmlspecialchars($_SESSION['success_message']) ?></h2>
            <button id="closeSuccessModal">OK</button>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
<?php endif; ?>


<!-- Footer -->
<?php include '../footer.php'; ?>
    <script>
        
// Select all tab buttons and content sections
const tabs = document.querySelectorAll('.tab-button');
const all_content = document.querySelectorAll('.tab-content');

// Add click event listeners to each tab button
tabs.forEach((tab, index) => {
    tab.addEventListener('click', (event) => {
        // Remove 'active' class from all tab buttons and hide all content
        tabs.forEach(tab => tab.classList.remove('active'));
        all_content.forEach(content => content.classList.remove('active-content'));

        // Add 'active' class to the clicked tab and corresponding content
        tab.classList.add('active');
        all_content[index].classList.add('active-content');
    });
});

    
    </script>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this tutorial?");
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successModal = document.getElementById('successModal');
        const closeBtn = document.getElementById('closeSuccessModal');

        if (successModal) {
            closeBtn.addEventListener('click', function () {
                successModal.style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.onclick = function (event) {
                if (event.target === successModal) {
                    successModal.style.display = 'none';
                }
            };
        }
    });
</script>
</body>
</html>

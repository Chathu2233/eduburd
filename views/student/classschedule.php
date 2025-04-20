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
            CONCAT(u.first_name, ' ', u.last_name) AS tutor_name
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

    // Fetch assignments for the grade_class_id
    $stmt_assignments = $pdo->prepare("
        SELECT 
            assignment_id, 
            title, 
            description, 
            deadline,
            file,
            (SELECT COUNT(*) FROM assignment_submission WHERE assignment_id = a.assignment_id) AS is_submitted
        FROM assignment a
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt_assignments->execute([':grade_class_id' => $grade_class_id]);
    $assignments = $stmt_assignments->fetchAll(PDO::FETCH_ASSOC);

    // Sort assignments: Unsubmitted ones first
    usort($assignments, function ($a, $b) {
        return $a['is_submitted'] - $b['is_submitted'];
    });

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
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


</head>
<body>

    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <script src="script.js"></script>


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

    <div class = "announcement">
        <h2>Announcement</h2>
        <div class = "announcement-content">
            <p>

Dear Students,<br>

We are excited to share the updated class schedule for the upcoming week! 🎓<br><br>

📅 Schedule Highlights:<br>

Ensure to join the class 5 minutes early to avoid disruptions.<br>
For any scheduling conflicts or queries, feel free to reach out to us through the EduBurd Help Center.<br>
We look forward to your active participation! Let’s make learning fun and engaging. </p>
        </div>
    </div>
    <div class="content-section">
        <div class="bookclass">
            <h2>Class Booking</h2>
            <div class="booking-container">
                <div class="booking-form">
                    <label for="date">Select Date:</label>
                    <input type="date" id="date" name="date">
    
                    <label for="duration">Class Duration:</label>
                    <select id="duration" name="duration">
                        <option value="30">30 Minutes</option>
                        <option value="60">1 Hour</option>
                        <option value="90">1.5 Hours</option>
                        <option value="120">2 Hours</option>
                        <option value="150">2.5 hours</option>
                        <option value="180">3 hours</option>
                    </select>
    
                    <label for="country">Select Country:</label>
                    <select id="country" name="country">
                        <option value="UTC">UTC</option>
                        <option value="America/New_York">USA (Eastern Time)</option>
                        <option value="Europe/London">UK (London Time)</option>
                        <option value="Asia/Tokyo">Japan (Tokyo Time)</option>
                        <!-- Add more countries as needed -->
                    </select>
    
                    <button onclick="showAvailableTimes()">Check Availability</button>
                </div>
    
                <div class="availability">
                    <h3>Teacher Available Hours</h3>
                    <div id="available-times"></div>
                </div>
            </div>
        </div>
    </div>
    

      
    
    <div class="content-section">
            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'class-schedule')">Class Schedule</button>
                <button class="tab-button" onclick="openTab(event, 'join-class')">Join Class</button>
                <button class="tab-button" onclick="openTab(event, 'tutorials')">Assignments</button>
            </div>

            <div id="class-schedule" class="tab-content active-content">
                <h2>Class Schedule</h2>
                <p>2024-10-10<button>Cancel Class</button></p>
                <p>2024-10-15<button>Cancel Class</button></p>
                
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
                                        <span class="status <?php echo $assignment['is_submitted'] > 0 ? 'submitted' : (date('Y-m-d') > $assignment['deadline'] ? 'closed' : ''); ?>">
                                            <?php if ($assignment['is_submitted'] > 0): ?>
                                                ✔ Submitted
                                            <?php elseif (date('Y-m-d') > $assignment['deadline']): ?>
                                                Submission Closed
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($assignment['is_submitted'] == 0 && date('Y-m-d') <= $assignment['deadline']): ?>
                                            <button class="submit-btn" onclick="window.location.href='submission.php?assignment_id=<?php echo $assignment['assignment_id']; ?>'">Submit</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p><?php echo htmlspecialchars($assignment['description']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($assignment['deadline']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            
        </div>
    </div>
    <?php include '../footer.php'; ?>
    <script>
        
// Select all tab buttons and content sections
const tabs = document.querySelectorAll('.tab-button');
const all_content = document.querySelectorAll('.tab-content');

// Add click event listeners to each tab button
tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
        // Remove 'active' class from all tab buttons and hide all content
        tabs.forEach(tab => tab.classList.remove('active'));
        all_content.forEach(content => content.classList.remove('active-content'));

        // Add 'active' class to the clicked tab and corresponding content
        tab.classList.add('active');
        all_content[index].classList.add('active-content');
    });
});

function showAvailableTimes() {
    const date = document.getElementById("date").value;
    const duration = parseInt(document.getElementById("duration").value); // Use this single duration selector
    const country = document.getElementById("country").value;

    if (!date || !duration || !country) {
        alert("Please select all fields.");
        return;
    }

    // Sample teacher availability hours in UTC (adjust as needed)
    const teacherAvailability = [
        { start: "09:00", end: "12:00" },
        { start: "14:00", end: "18:00" }
    ];

    const options = { timeZone: country, hour: '2-digit', minute: '2-digit', hour12: false };
    let availableTimesHTML = "";

    teacherAvailability.forEach(slot => {
        const startDate = new Date(`${date}T${slot.start}:00Z`);
        const endDate = new Date(`${date}T${slot.end}:00Z`);

        // Convert to the selected timezone
        const startLocal = startDate.toLocaleTimeString('en-US', options);
        const endLocal = endDate.toLocaleTimeString('en-US', options);

        // Format duration end time based on the start time and selected duration
        const durationEnd = new Date(startDate);
        durationEnd.setMinutes(durationEnd.getMinutes() + duration);
        const durationEndLocal = durationEnd.toLocaleTimeString('en-US', options);

        availableTimesHTML += `
            <p><strong>Available Slot:</strong> ${startLocal} - ${endLocal}</p>
            <p><strong>Class Time:</strong> ${startLocal} - ${durationEndLocal} <button id="booking" ><a href="payment.php">Book slot</a></button></p>
            <p id="amount">Amount: $${(duration / 30) * 5.00}</p>
            <hr>
        `;
    });

    document.getElementById("available-times").innerHTML = availableTimesHTML;
    document.querySelector(".availability").style.display = "block";
}

// Update amount when the duration changes in the form
document.getElementById("duration").addEventListener("change", () => {
    const duration = parseFloat(document.getElementById("duration").value);
    const pricePerHalfHour = 5.00;
    const totalAmount = (duration / 30) * pricePerHalfHour;

    document.querySelector("#amount").textContent = `Amount: $${totalAmount.toFixed(2)}`;
});



    </script>
</body>
</html>

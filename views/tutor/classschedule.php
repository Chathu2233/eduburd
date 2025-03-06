
<?php

    session_start();
    // Include the database connection
    require '../Database.php';
    require '../config.php';


    class TutorialDashboard {
    use Database;

    public function fetchTutorials() {
        $sql = "SELECT tutorial_id, title FROM tutorial";
        $stmt = $this->connect()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteTutorial($tutorial_id) {
        $sql = "DELETE FROM tutorial WHERE tutorial_id = :tutorial_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':tutorial_id' => $tutorial_id]);
    }
}

$dashboard = new TutorialDashboard();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tutorial_id'])) {
    $dashboard->deleteTutorial($_POST['delete_tutorial_id']);
    echo '<p style="color: green; text-align: center;">Tutorial deleted successfully!</p>';
}

// Fetch all tutorials
$tutorials = $dashboard->fetchTutorials();

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
        <img src="../../assets/images/dashboard.png" alt="Centered images"  width="50" height="50" style="margin-top: 30px; "  style="background-color: pink;">
        <ul>
        <div class="sidebar1">
            <li><a href="my_account.php"><i class="fas fa-user"></i> My Profile</a></li>
        </div>

        <div class="sidebar2">
            <li><a href="subject.php"><i class="fas fa-tachometer-alt"></i> My subjects</a></li>
        </div>

        <div class="sidebar3">
            <li><a href="contact_parent.php"><i class="fas fa-user-plus"></i> Contact Parent</a></li>
        </div>

        <div class="sidebar3">
            <li><a href="view_student.php"><i class="fas fa-edit"></i> View Student Profile </a></li>
        </div>

        <div class="sidebar4">
            <li><a href="comment.php"><i class="fas fa-edit"></i> View Comments </a></li>
        </div>
        <div class="sidebar5">
            <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
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
                <h3>This week</h3>
                <div class="class-item">
                    <span>25-08-2024</span>
                    <button class="join-now">Join now</button>
                </div>
                <div class="class-item">
                    <span>25-08-2024</span>
                    <button class="join-now">Join now</button>
                </div>
            </div>

                
            </div>

            <div id="tutorials" class="tab-content">
                <h3>Tutorials</h3>
                <div class="tutorial-controls">
                <button><a href="addtutorial.php" class="addtutorial">Add Tutorial</a></button>
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
                    <button><a href="addassignment.php" class="addassignment">Add Assignment</a></button>
                    </div>
                        <div class="assignment-item">Assignment 1 
                        <button class="submit"><a href = view_submission.php>Submissions</button>
                        <button class="edit"><a href = editassignment.php>Edit</button>
                            <button class="delete">Delete</button>
                        </div>
                        <div class="assignment-item">Assignment 2
                        <button class="submit"><a href = view_submission.php>Submissions</button>
                        <button class="edit"><a href = editassignment.php>Edit</button>
                            <button class="delete">Delete</button>
                        </div>
                </div>
            </div>

            
    <!-- View Progress Section -->
    <div id="view-progress" class="tab-content">
        <!-- Student Info -->
        <section class="student-info">
            <img src="../../assets/images/student.jpg" alt="Student Profile" class="student-avatar">
            <div class="student-details">
                <h1>Ayathma Amanethmi</h1>
                <p><strong>Grade:</strong> 10</p>
                <p><strong>Email:</strong> amaamanethmi@gmail.com</p>
                <p><strong>Contact:</strong> +94 705043255</p>
            </div>
        </section>

        <!-- Progress Table -->
        <section class="progress-table">
            <h2>Progress Table</h2>
            <table>
                <thead>
                    <tr>
                        <th>Assignment No</th>
                        <th>Marks</th>
                        <th>Progress (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>75</td>
                        <td>---</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>80</td>
                        <td>+6.67%</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>85</td>
                        <td>+6.25%</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Progress Chart -->
        <section class="progress-chart">
            <h2>Progress Chart</h2>
            <div class="chart-placeholder">
                <div class="bar" style="width: 75%; background-color: #4caf50;">Assignment 1: 75</div>
                <div class="bar" style="width: 80%; background-color: #2196f3;">Assignment 2: 80</div>
                <div class="bar" style="width: 85%; background-color: #ff9800;">Assignment 3: 85</div>
            </div>
        </section>
    </div>
                    
        </div>
 

</div>


   
</div>
</body>
</html>

    </body>
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
    function confirmDelete() {
        return confirm("Are you sure you want to delete this tutorial?");
    }

    // Show success modal if deletion was successful
    <?php if (isset($_POST['delete_tutorial_id'])): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const successModal = document.getElementById('successModal');
            successModal.style.display = 'block';

            const closeBtn = document.getElementById('closeSuccessModal');
            closeBtn.addEventListener('click', function () {
                successModal.style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.onclick = function (event) {
                if (event.target === successModal) {
                    successModal.style.display = 'none';
                }
            };
        });
    <?php endif; ?>
</script>
</body>
</html>

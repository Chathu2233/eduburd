<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Tutors</title>

    <link rel="stylesheet" href="../../assets/css/student/tutor.css"> 
    
</head>
<body>
   
        <!-- Header Section -->
        <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
        
 


    <!-- Tutors Section -->
    <main>
        <div class="tutors-container">
            <h2>Course Tutors</h2>
            <p>Here are the tutors for this course:</p>

            <div class="tutors-list">
                <!-- Tutor 1 -->
                <div class="tutor">
                    <h4>Tutor Name 1</h4>
                    <p>Specialization: Mathematics</p>
                    <p>Email: tutor1@example.com</p>
                    <a href="classschedule.php">View Class Schedule</a>
                </div>
                <!-- Tutor 2 -->
                <div class="tutor">
                    <h4>Tutor Name 2</h4>
                    <p>Specialization: Science</p>
                    <p>Email: tutor2@example.com</p>
                    <a href="classschedule.php">View Class Schedule</a>
                </div>
                <!-- Tutor 3 -->
                <div class="tutor">
                    <h4>Tutor Name 3</h4>
                    <p>Specialization: History</p>
                    <p>Email: tutor3@example.com</p>
                    <a href="classschedule.php">View Class Schedule</a>
                </div>
                <div class="tutor">
                    <h4>Tutor Name 3</h4>
                    <p>Specialization: History</p>
                    <p>Email: tutor3@example.com</p>
                    <a href="classschedule.php">View Class Schedule</a>
                </div>
                <div class="tutor">
                    <h4>Tutor Name 3</h4>
                    <p>Specialization: History</p>
                    <p>Email: tutor3@example.com</p>
                    <a href="classschedule.php">View Class Schedule</a>
                </div>
            </div>
        </div>
    </main>

 
</body>
<?php include '../footer.php'; ?>  
</html>

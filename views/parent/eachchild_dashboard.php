<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>each child dashboard</title>
   

    <link rel="stylesheet" href="../../assets/css/parent/dashboard.css">
    
   

    <link rel="stylesheet" href="../../assets/css/parent/eachchild_dashboard.css">
     

</head>

<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>
    
    
    <!-- Main Layout Container -->
    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="../../assets/images/pic-5.jpg" alt="Centered images" width="50" height="50" style="margin-top: 30px;">
            <ul>
                <div class="sidebar1">
                    <li><a href="childprofile.php"><i class="fas fa-user"></i> Child Profile</a></li>
                </div>
                <div class="sidebar1">
                    <li><a href="eachchild_dashboard.php"><i class="fas fa-tachometer-alt"></i> Subjects enrolled</a></li>
                </div>
                <div class="sidebar1">
                    <li><a href="submissionstatus.php"><i class="fas fa-user-plus"></i> Submission status</a></li>
                </div>
                <div class="sidebar1">
                    <li><a href="classschedule.php"><i class="fas fa-book"></i> Class Schedule</a></li>
                </div>

                <div class="sidebar1">
                    <li><a href="progressreport.php"><i class="fas fa-book"></i> Progress report </a></li>
                </div>
              
              
            </ul>
        </div>

        
<!-- Main Content: Child's Enrolled Subjects -->
        <div class="container">
            <h1>Your child's enrolled subjects</h1>
            <p class="description">As a parent, you can review the subjects your child has enrolled in for the academic year.</p>
            <ul id="subjectsList">
                <li>
                    <div class="subject-content">
                        <img src="../../assets/images/english.png" alt="English Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>English</span>
                            <p class="subject-desc">Develop your child's language skills, including grammar, vocabulary, and reading comprehension.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
                <li>
                    <div class="subject-content">
                        <img src="../../assets/images/maths.jfif" alt="Math Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>Math</span>
                            <p class="subject-desc">Help your child master mathematical concepts, from algebra to calculus.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
                <li>
                    <div class="subject-content">
                        <img src="../../assets/images/science.jfif" alt="Science Icon" class="subject-icon">
                        <div class="subject-details">
                            <span>Science</span>
                            <p class="subject-desc">Explore physics, chemistry, and biology with hands-on experiments and theories.</p>
                        </div>
                    </div>
                    <a href="seetutor.php" class="view-details">View Details</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

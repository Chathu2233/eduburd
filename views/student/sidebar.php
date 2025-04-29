<?php
$grade_class_id = $_GET['grade_class_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Find a Tutor</title>
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <div class="sidebar">
        <img src="../../assets/images/dashboard.png" alt="Dashboard Logo" width="50" height="50" style="margin-top: 30px;">
        <ul>
            <div class="sidebar1">
                <li><a href="myprofile.php">My Profile</a></li>
            </div>
            <div class="sidebar2">
                <li><a href="stu_dashboard.php">My Subjects</a></li>
            </div>
            <div class="sidebar3">
                <li><a href="mytutors.php?student_id=<?php echo htmlspecialchars($student_id); ?>">My Tutors</a></li>
            </div>
        
            <div class="sidebar3">
                <li><a href="myparent.php">My Parent</a></li>
            </div>
            <div class="sidebar4">
                <li><a href="resourcelibrary.php">Resource Library</a></li>
            </div>
            <div class="sidebar5">
                <li><a href="viewannouncement.php">Announcements</a></li>
            </div>
            
            <div class="sidebar7">
                <li><a href="editprofile.php">Edit Profile</a></li>
            </div>
        </ul>
    </div>
</body>
</html>

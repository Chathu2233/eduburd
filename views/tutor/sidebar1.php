<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/sidebar.css">
</head>
<body>
    
<div class="sidebar">
    <a href="classschedule.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>">
        <img src="../../assets/images/dashboard.png" alt="Dashboard" width="50" height="50" style="margin-top: 30px;">
    </a>
    <ul>
    <div class="sidebar5">
            <li><a href="announcement.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"> Announcements</a></li>
        </div>
        <div class="sidebar3">
            <li><a href="view_student.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>">Student Profile</a></li>
        </div>
        <div class="sidebar3">
            <li><a href="student_feedback.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"> Student Feedback</a></li>
        </div>
        <div class="sidebar3">
            <li><a href="contact_parent.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"> Contact Parent</a></li>
        </div>
        
        <div class="sidebar4">
            <li><a href="comment.php?grade_class_id=<?= htmlspecialchars($grade_class_id) ?>"> Parent Comments</a></li>
        </div>
        <div class="sidebar6">
            <li><a href="../resourcelibrary.php"> Resource Library</a></li>
        </div>
    </ul>
</div>
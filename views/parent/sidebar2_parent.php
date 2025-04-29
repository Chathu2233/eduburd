<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="sidebar">
<img src="<?php echo ROOT; ?>/assets/images/dashboard.png" alt="Dashboard Logo" width="50" height="50" style="margin-top: 30px;">
<ul>
        <div class="sidebar1">
            <li><a href="dashboard.php"><i class="fas fa-user"></i> My Dashboard</a></li>
        </div>
        <div class="sidebar3">
            <li><a href="childlist.php">My Child List</a></li>
        </div>
        <div class="sidebar1">
            <li><a href="childprofile.php"><i class="fas fa-user"></i> Child Profile</a></li>
        </div>
        <div class="sidebar1">
            <li><a href="eachchild_dashboard.php"><i class="fas fa-tachometer-alt"></i> Subjects enrolled</a></li>
        </div>
        <div class="sidebar1">
            <li><a href="submissionstatus.php?student_id=<?= $_SESSION['student_id'] ?>"<i class="fas fa-book"></i>Submission status</a></li>
        </div>
        <div class="sidebar1">
            <li><a href="classschedule.php?student_id=<?= $_SESSION['student_id'] ?>"<i class="fas fa-book"></i> Class Schedule</a></li>
        </div>
        <div class="sidebar1">
            <li><a href="progressreport.php?student_id=<?= $_SESSION['student_id'] ?>"><i class="fas fa-book"></i> Progress report</a></li>
        </div>
    </ul>
</div>
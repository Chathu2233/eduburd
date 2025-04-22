<div class="sidebar">
    <ul>
        <div class="sidebar1">
            <li>
                <a href="dashboard.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-user"></i> My Dashboard
                </a>
            </li>
        </div>
        <div class="sidebar3">
            <li>
                <a href="childlist.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    My Child List
                </a>
            </li>
        </div>
        <div class="sidebar1">
            <li>
                <a href="seetutor.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-user"></i> Tutor Profile
                </a>
            </li>
        </div>
        <div class="sidebar5">
            <li>
                <a href="addcomment.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-user"></i> Add Comment
                </a>
            </li>
        </div>
        <div class="sidebar1">
            <li>
                <a href="classhistory.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-user"></i> Class History
                </a>
            </li>
        </div>
        <div class="sidebar2">
            <li>
                <a href="upcomingclasses.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-book"></i> Class Schedule
                </a>
            </li>
        </div>
        <div class="sidebar3">
            <li>
                <a href="paymenthistory.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    <i class="fas fa-user-plus"></i> Payment History
                </a>
            </li>
        </div>
        <div class="sidebar3">
            <li>
                <a href="pendingassignment.php?student_id=<?= $_SESSION['student_id'] ?>&course_id=<?= $_SESSION['course_id'] ?>&tutor_id=<?= $_SESSION['tutor_id'] ?>">
                    Pending Assignment
                </a>
            </li>
        </div>
    </ul>
</div>
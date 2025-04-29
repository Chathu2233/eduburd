<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch all students for the logged-in tutor
$search_query = '';
$filter_grade = '';
$filter_course = '';
$students = [];

// Fetch all grades for the dropdown
try {
    $stmt_grades = $pdo->prepare("SELECT DISTINCT grade FROM grade ORDER BY grade ASC");
    $stmt_grades->execute();
    $grades = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching grades: " . $e->getMessage());
}

// Fetch all courses for the dropdown
try {
    $stmt_courses = $pdo->prepare("SELECT DISTINCT name FROM course ORDER BY name ASC");
    $stmt_courses->execute();
    $courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

try {
    $sql = "
        SELECT 
            s.student_id,
            u.first_name,
            u.last_name,
            u.email,
            g.grade,
            c.name AS course_name
        FROM 
            grade_class gc
        JOIN 
            student s ON gc.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        JOIN 
            grade g ON gc.grade_id = g.grade_id
        JOIN 
            course c ON gc.course_id = c.course_id
        WHERE 
            gc.tutor_id = :tutor_id
    ";

    // Add search and filter conditions
    if (!empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
        $sql .= " AND (u.first_name LIKE :search_query OR u.last_name LIKE :search_query)";
    }

    if (!empty($_GET['filter_grade'])) {
        $filter_grade = $_GET['filter_grade'];
        $sql .= " AND g.grade = :filter_grade";
    }

    if (!empty($_GET['filter_course'])) {
        $filter_course = $_GET['filter_course'];
        $sql .= " AND c.name = :filter_course";
    }

    $sql .= " ORDER BY u.first_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);

    if (!empty($search_query)) {
        $search_query = '%' . $search_query . '%';
        $stmt->bindParam(':search_query', $search_query, PDO::PARAM_STR);
    }

    if (!empty($filter_grade)) {
        $stmt->bindParam(':filter_grade', $filter_grade, PDO::PARAM_STR);
    }

    if (!empty($filter_course)) {
        $stmt->bindParam(':filter_course', $filter_course, PDO::PARAM_STR);
    }

    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching students: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="../../assets/css/Tutor/all_student.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>
<div class="container">
    <?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

    <!-- Main Content Section -->
    <main class="content-section">
        <div class="content">
            <!-- Student Section -->
        <section class="student-section">
            <h2>👩‍🎓 View All Students</h2>
            <p>Search and filter students assigned to you.</p>

            <!-- Search and Filter Form -->
            <form method="GET" action="all_student.php" class="search-filter-form">
                <input type="text" name="search_query" placeholder="Search by name..." value="<?= htmlspecialchars($_GET['search_query'] ?? '') ?>">
                
                <!-- Filter by Grade -->
                <select name="filter_grade">
                    <option value="">Filter by Grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= htmlspecialchars($grade['grade']) ?>" <?= (isset($_GET['filter_grade']) && $_GET['filter_grade'] === $grade['grade']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grade['grade']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Filter by Course -->
                <select name="filter_course">
                    <option value="">Filter by Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course['name']) ?>" <?= (isset($_GET['filter_course']) && $_GET['filter_course'] === $course['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="search-btn">Search</button>
            </form>

            <!-- Students Table -->
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Grade</th>
                        <th>Course</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['grade']) ?></td>
                                <td><?= htmlspecialchars($student['course_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-students-message">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
                    </div>
<?php include '../footer.php'; ?>
</body>
</html>
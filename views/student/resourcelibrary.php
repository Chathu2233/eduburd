<?php
session_start();
// Include database connection
require_once '../db.php';

// Fetch grades and courses for the filter dropdowns
try {
    $grades_stmt = $pdo->prepare("SELECT DISTINCT grade FROM resource_library ORDER BY grade ASC");
    $grades_stmt->execute();
    $grades = $grades_stmt->fetchAll(PDO::FETCH_ASSOC);

    $courses_stmt = $pdo->prepare("SELECT DISTINCT course FROM resource_library WHERE course != '' ORDER BY course ASC");
    $courses_stmt->execute();
    $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch resources based on filters
$grade_filter = $_GET['grade'] ?? 'All Grades';
$course_filter = $_GET['course'] ?? 'All Courses';

try {
    $query = "SELECT * FROM resource_library WHERE 1=1";
    $params = [];

    if ($grade_filter !== 'All Grades') {
        $query .= " AND grade = :grade";
        $params[':grade'] = $grade_filter;
    }

    if ($course_filter !== 'All Courses') {
        $query .= " AND course = :course";
        $params[':course'] = $course_filter;
    }

    $query .= " ORDER BY title ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Resource Library</title>
    <link rel="stylesheet" href="../../assets/css/student/resourcelibrary.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <?php
        // Dynamically include the correct header based on user role
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include '../header_admin.php';
                    break;
                case 'student':
                    include '../header_student.php';
                    break;
                case 'tutor':
                    include '../header_tutor.php';
                    break;
                case 'parent':
                    include '../header_parent.php';
                    break;
                default:
                    include '../header_guest.php'; // Fallback for unknown roles
            }
        } else {
            include '../header_guest.php'; // For guests (not logged in)
        }
        ?>
    </header>

    <!-- Page Content -->
    <div class="content-wrapper">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <p>Homepage &gt; Resource Library</p>
        </div>

        <!-- Filters and Resource Section -->
        <div class="resource-container">
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <h2>Filter Resources</h2>
                <form method="GET" action="">
                    <div class="filter">
                        <label for="grade">Grade</label>
                        <select id="grade" name="grade">
                            <option value="All Grades" <?php echo $grade_filter === 'All Grades' ? 'selected' : ''; ?>>All Grades</option>
                            <?php foreach ($grades as $grade): ?>
                                <option value="<?php echo htmlspecialchars($grade['grade']); ?>" <?php echo $grade_filter == $grade['grade'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($grade['grade']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="course">Course</label>
                        <select id="course" name="course">
                            <option value="All Courses" <?php echo $course_filter === 'All Courses' ? 'selected' : ''; ?>>All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course['course']); ?>" <?php echo $course_filter == $course['course'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </aside>

            <!-- Resource Cards -->
            <main class="resource-list">
                <?php if (empty($resources)): ?>
                    <p>No resources found for the selected filters.</p>
                <?php else: ?>
                    <?php foreach ($resources as $resource): ?>
                        <div class="resource" data-type="<?php echo htmlspecialchars($resource['type']); ?>">
                            <img src="../../assets/images/<?php echo htmlspecialchars($resource['type']); ?>.png" alt="Resource Icon">
                            <div class="resource-info">
                                <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                <p><?php echo htmlspecialchars($resource['description']); ?></p>
                                <p>Grade: <?php echo htmlspecialchars($resource['grade']); ?></p>
                                <p>Course: <?php echo htmlspecialchars($resource['course']); ?></p>
                                <p>Format: <?php echo htmlspecialchars(ucwords($resource['type'])); ?></p>
                                <?php if ($resource['file_path']): ?>
                                    <a href="resources/<?php echo htmlspecialchars($resource['file_path']); ?>" download>
                                        <button>Download</button>
                                    </a>
                                <?php else: ?>
                                    <p>No file available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../footer.php'; ?>

</body>
</html>

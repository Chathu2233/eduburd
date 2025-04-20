<?php
session_start();
include '../db.php';
require_once '../constants.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch counts for dashboard
$countsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM user) AS total_users,
        (SELECT COUNT(*) FROM user WHERE user_role = 'tutor') AS total_tutors,
        (SELECT COUNT(*) FROM user WHERE user_role = 'student') AS total_students,
        (SELECT COUNT(*) FROM user WHERE user_role = 'parent') AS total_parents,
        (SELECT COUNT(*) FROM user WHERE user_role = 'admin') AS total_admins,
        (SELECT COUNT(*) FROM payment) AS total_payments,
        (SELECT COUNT(*) FROM grade_class) AS total_classes
";
$countsStmt = $pdo->prepare($countsQuery);
$countsStmt->execute();
$counts = $countsStmt->fetch(PDO::FETCH_ASSOC);

// Apply filters for user data
$roleFilter = isset($_GET['role']) && $_GET['role'] !== '' ? $_GET['role'] : null;
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

$userQuery = "SELECT user_id, first_name, last_name, email, created_at FROM user WHERE 1=1";
$params = [];

if ($roleFilter) {
    $userQuery .= " AND user_role = :role";
    $params['role'] = $roleFilter;
}
if ($startDate) {
    $userQuery .= " AND created_at >= :start_date";
    $params['start_date'] = $startDate;
}
if ($endDate) {
    $userQuery .= " AND created_at <= :end_date";
    $params['end_date'] = $endDate;
}

$userQuery .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($userQuery);
$stmt->execute($params);
$filteredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch earnings data
$earningsQuery = "SELECT payment_id, class_id, amount, date, method FROM payment ORDER BY date DESC";
$earningsStmt = $pdo->prepare($earningsQuery);
$earningsStmt->execute();
$earnings = $earningsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch class data
$classesByTutorQuery = "
    SELECT 
        t.tutor_id, CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        COUNT(gc.grade_class_id) AS total_classes, SUM(gc.duration) AS total_hours
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    GROUP BY t.tutor_id ORDER BY total_classes DESC
";
$classesByTutorStmt = $pdo->prepare($classesByTutorQuery);
$classesByTutorStmt->execute();
$classesByTutor = $classesByTutorStmt->fetchAll(PDO::FETCH_ASSOC);

$classesByCourseQuery = "
    SELECT c.name AS course_name, COUNT(gc.grade_class_id) AS total_classes, SUM(gc.duration) AS total_hours
    FROM grade_class gc
    JOIN course c ON gc.course_id = c.course_id
    GROUP BY c.name ORDER BY total_classes DESC
";
$classesByCourseStmt = $pdo->prepare($classesByCourseQuery);
$classesByCourseStmt->execute();
$classesByCourse = $classesByCourseStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIS Reports</title>
    <link rel="stylesheet" href="../../assets/css/admin/viewanalytics.css">
</head>
<body>
<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="content">
    <h1>MIS Reports</h1>

    <!-- Filter Form -->
    <form method="GET" action="viewanalytics.php" class="filter-form">
        <div class="filter-group">
            <label for="role">User Role:</label>
            <select name="role" id="role">
                <option value="">All</option>
                <option value="tutor" <?php echo (isset($_GET['role']) && $_GET['role'] === 'tutor') ? 'selected' : ''; ?>>Tutor</option>
                <option value="student" <?php echo (isset($_GET['role']) && $_GET['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="parent" <?php echo (isset($_GET['role']) && $_GET['role'] === 'parent') ? 'selected' : ''; ?>>Parent</option>
                <option value="admin" <?php echo (isset($_GET['role']) && $_GET['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        </div>
        <button type="submit" class="filter-btn">Filter</button>
    </form>

    <!-- Dashboard Section -->
    <section class="dashboard-section">
        <h2>Dashboard</h2>
        <div class="dashboard-cards">
            <?php foreach ($counts as $key => $value): ?>
                <div class="card">
                    <h3><?php echo ucwords(str_replace('_', ' ', $key)); ?></h3>
                    <p><?php echo $value; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- User Reports -->
    <section class="report-section">
        <h2>Filtered Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filteredUsers as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Earnings Report -->
    <section class="report-section">
        <h2>Earnings Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Class ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($earnings as $earning): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($earning['payment_id']); ?></td>
                        <td><?php echo htmlspecialchars($earning['class_id'] ?? 'N/A'); ?></td>
                        <td>$<?php echo htmlspecialchars($earning['amount']); ?></td>
                        <td><?php echo htmlspecialchars($earning['date']); ?></td>
                        <td><?php echo htmlspecialchars($earning['method']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Classes by Tutor -->
    <section class="report-section">
        <h2>Classes by Tutor</h2>
        <table>
            <thead>
                <tr>
                    <th>Tutor ID</th>
                    <th>Tutor Name</th>
                    <th>Total Classes</th>
                    <th>Total Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classesByTutor as $class): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class['tutor_id']); ?></td>
                        <td><?php echo htmlspecialchars($class['tutor_name']); ?></td>
                        <td><?php echo htmlspecialchars($class['total_classes']); ?></td>
                        <td><?php echo htmlspecialchars($class['total_hours']); ?> hours</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Classes by Course -->
    <section class="report-section">
        <h2>Classes by Course</h2>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Total Classes</th>
                    <th>Total Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classesByCourse as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['total_classes']); ?></td>
                        <td><?php echo htmlspecialchars($course['total_hours']); ?> hours</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Download CSV Button -->
    <form method="GET" action="download_csv.php">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        <button type="submit" class="export-btn">Download CSV</button>
    </form>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
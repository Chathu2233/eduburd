<?php
session_start();
include '../db.php';
require_once '../constants.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch the latest 10 users
$latestUsersQuery = "SELECT user_id, first_name, last_name, email, created_at FROM user ORDER BY created_at DESC LIMIT 10";
$latestUsersStmt = $pdo->prepare($latestUsersQuery);
$latestUsersStmt->execute();
$latestUsers = $latestUsersStmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the request is an AJAX request to fetch all users
if (isset($_GET['action']) && $_GET['action'] === 'fetch_all_users') {
    $allUsersQuery = "SELECT user_id, first_name, last_name, email, created_at FROM user ORDER BY created_at DESC";
    $allUsersStmt = $pdo->prepare($allUsersQuery);
    $allUsersStmt->execute();
    $allUsers = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($allUsers);
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

if (!empty($_GET['role'])) {
    $userQuery .= " AND user_role = :role";
    $params[':role'] = $_GET['role'];
}
if (!empty($_GET['start_date'])) {
    $userQuery .= " AND created_at >= :start_date";
    $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $userQuery .= " AND created_at <= :end_date";
    $params[':end_date'] = $_GET['end_date'];
}
if (!empty($_GET['search'])) {
    $userQuery .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $_GET['search'] . '%';
}

$userQuery .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($userQuery);
$stmt->execute($params);
$filteredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch earnings data
$paymentQuery = "SELECT payment_id, grade_class_id, amount, date, method FROM payment WHERE 1=1";
$params = [];

if (!empty($_GET['payment_method'])) {
    $paymentQuery .= " AND method = :payment_method";
    $params[':payment_method'] = $_GET['payment_method'];
}
if (!empty($_GET['start_date'])) {
    $paymentQuery .= " AND date >= :start_date";
    $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $paymentQuery .= " AND date <= :end_date";
    $params[':end_date'] = $_GET['end_date'];
}
if (!empty($_GET['min_amount'])) {
    $paymentQuery .= " AND amount >= :min_amount";
    $params[':min_amount'] = $_GET['min_amount'];
}
if (!empty($_GET['max_amount'])) {
    $paymentQuery .= " AND amount <= :max_amount";
    $params[':max_amount'] = $_GET['max_amount'];
}

$stmt = $pdo->prepare($paymentQuery);
$stmt->execute($params);
$filteredPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch class data
$classesByTutorQuery = "
    SELECT 
        t.tutor_id, 
        CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        COUNT(gc.grade_class_id) AS total_classes, 
        COUNT(gc.grade_class_id) * 2 AS total_hours
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    GROUP BY t.tutor_id 
    ORDER BY total_classes DESC
";
$classesByTutorStmt = $pdo->prepare($classesByTutorQuery);
$classesByTutorStmt->execute();
$classesByTutor = $classesByTutorStmt->fetchAll(PDO::FETCH_ASSOC);

$classesByCourseQuery = "
    SELECT 
        c.name AS course_name, 
        COUNT(gc.grade_class_id) AS total_classes, 
        COUNT(gc.grade_class_id) * 2 AS total_hours
    FROM grade_class gc
    JOIN course c ON gc.course_id = c.course_id
    GROUP BY c.name 
    ORDER BY total_classes DESC
";
$classesByCourseStmt = $pdo->prepare($classesByCourseQuery);
$classesByCourseStmt->execute();
$classesByCourse = $classesByCourseStmt->fetchAll(PDO::FETCH_ASSOC);

$classQuery = "
    SELECT 
        gc.grade_class_id, 
        t.tutor_id, 
        c.course_id, 
        g.grade_id, 
        gc.day, 
        gc.time, 
        gc.description
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN course c ON gc.course_id = c.course_id
    JOIN grade g ON gc.grade_id = g.grade_id
    WHERE 1=1
";
$params = [];

if (!empty($_GET['tutor_id'])) {
    $classQuery .= " AND gc.tutor_id = :tutor_id";
    $params[':tutor_id'] = $_GET['tutor_id'];
}
if (!empty($_GET['course_id'])) {
    $classQuery .= " AND c.name LIKE :course_name";
    $params[':course_name'] = '%' . $_GET['course_id'] . '%';
}
if (!empty($_GET['grade_id'])) {
    $classQuery .= " AND g.grade_id = :grade_id";
    $params[':grade_id'] = $_GET['grade_id'];
}
if (!empty($_GET['start_date'])) {
    $classQuery .= " AND gc.day >= :start_date";
    $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $classQuery .= " AND gc.day <= :end_date";
    $params[':end_date'] = $_GET['end_date'];
}

$stmt = $pdo->prepare($classQuery);
$stmt->execute($params);
$filteredClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total site earnings (20% of total payments)
$siteEarningsQuery = "SELECT SUM(amount) * 0.2 AS site_earnings FROM payment";
$siteEarningsStmt = $pdo->prepare($siteEarningsQuery);
$siteEarningsStmt->execute();
$siteEarnings = $siteEarningsStmt->fetch(PDO::FETCH_ASSOC)['site_earnings'] ?? 0; // Default to 0 if null
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

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
    <h1>MIS Reports</h1>

    <!-- Filter Form -->
    <form method="GET" action="viewanalytics.php" class="filter-form">
        <!-- User Filters -->
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

        <!-- Payment Filters -->
        <div class="filter-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method">
                <option value="">All</option>
                <option value="Credit Card" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                <option value="PayPal" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'PayPal') ? 'selected' : ''; ?>>PayPal</option>
                <option value="Bank Transfer" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="min_amount">Min Amount:</label>
            <input type="number" name="min_amount" id="min_amount" step="0.01" value="<?php echo htmlspecialchars($_GET['min_amount'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label for="max_amount">Max Amount:</label>
            <input type="number" name="max_amount" id="max_amount" step="0.01" value="<?php echo htmlspecialchars($_GET['max_amount'] ?? ''); ?>">
        </div>

        <!-- Class Filters -->
        <div class="filter-group">
            <label for="tutor_id">Tutor:</label>
            <input type="text" name="tutor_id" id="tutor_id" value="<?php echo htmlspecialchars($_GET['tutor_id'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label for="course_id">Course:</label>
            <input type="text" name="course_id" id="course_id" value="<?php echo htmlspecialchars($_GET['course_id'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label for="grade_id">Grade:</label>
            <input type="text" name="grade_id" id="grade_id" value="<?php echo htmlspecialchars($_GET['grade_id'] ?? ''); ?>">
        </div>

        <button type="submit" class="filter-btn">Filter</button>
    </form>

    
        
    <section class="dashboard-section">
        <h2>Dashboard</h2>
        <div class="dashboard-cards">
            <?php foreach ($counts as $key => $value): ?>
                <div class="card">
                    <h3><?php echo ucwords(str_replace('_', ' ', $key)); ?></h3>
                    <p><?php echo $value; ?></p>
                </div>
            <?php endforeach; ?>

          

            <!-- Site Earnings Card -->
            <div class="card">
                <h3>Site Earnings</h3>
                <p>$<?php echo number_format($siteEarnings, 2); ?> USD</p>
            </div>
        </div>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Earnings Report -->
    <section class="report-section">
        <h2>Earnings Report</h2>
        <?php if (empty($filteredPayments)): ?>
            <p>No results found.</p>
        <?php else: ?>
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
                    <?php foreach ($filteredPayments as $earning): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($earning['payment_id']); ?></td>
                            <td><?php echo htmlspecialchars($earning['grade_class_id'] ?? 'N/A'); ?></td>
                            <td>$<?php echo htmlspecialchars($earning['amount']); ?></td>
                            <td><?php echo htmlspecialchars($earning['date']); ?></td>
                            <td><?php echo htmlspecialchars($earning['method']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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

    <!-- Filtered Classes -->
    <section class="report-section">
        <h2>Filtered Classes</h2>
        <?php if (empty($filteredClasses)): ?>
            <p>No results found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Class ID</th>
                        <th>Tutor ID</th>
                        <th>Course ID</th>
                        <th>Grade ID</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredClasses as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['grade_class_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['tutor_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['grade_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['day']); ?></td>
                            <td><?php echo htmlspecialchars($class['time']); ?></td>
                            <td><?php echo htmlspecialchars($class['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
    <div class="section-spacing"></div> <!-- Add spacing between sections -->

    <!-- Latest Users -->
    <section class="report-section" id="users-section">
        <h2>Latest Users</h2>
        <table id="users-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestUsers as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button id="see-more-btn" class="see-more-btn">See More</button>
    </section>

    <!-- Download CSV Button -->
    <form method="GET" action="download_csv.php">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        <button type="submit" class="export-btn">Download CSV</button>
    </form>
</div>

<?php include '../footer.php'; ?>
<script>
document.getElementById('see-more-btn').addEventListener('click', function () {
    const seeMoreBtn = this;
    seeMoreBtn.disabled = true; // Disable the button to prevent multiple clicks
    seeMoreBtn.textContent = 'Loading...';

    // Make an AJAX request to fetch all users
    fetch('viewanalytics.php?action=fetch_all_users')
        .then(response => response.json())
        .then(data => {
            const usersTableBody = document.querySelector('#users-table tbody');
            usersTableBody.innerHTML = ''; // Clear the existing rows

            // Populate the table with all users
            data.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.user_id}</td>
                    <td>${user.first_name} ${user.last_name}</td>
                    <td>${user.email}</td>
                    <td>${user.created_at}</td>
                `;
                usersTableBody.appendChild(row);
            });

            // Remove the "See More" button after loading all users
            seeMoreBtn.remove();
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            seeMoreBtn.disabled = false; // Re-enable the button if there's an error
            seeMoreBtn.textContent = 'See More';
        });
});
</script>

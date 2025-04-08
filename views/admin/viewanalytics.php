<?php
session_start();
include '../db.php';
require_once '../constants.php';

// Fetch data from the database
$totalUsersQuery = "SELECT COUNT(*) AS totalUsers FROM user";
$totalUsersStmt = $pdo->prepare($totalUsersQuery);
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['totalUsers'];

$activeUsersQuery = "SELECT COUNT(*) AS activeUsers FROM user WHERE last_activity >= NOW() - INTERVAL 1 DAY";
$activeUsersStmt = $pdo->prepare($activeUsersQuery);
$activeUsersStmt->execute();
$activeUsers = $activeUsersStmt->fetch(PDO::FETCH_ASSOC)['activeUsers'];

$newRegistrationsQuery = "SELECT COUNT(*) AS newRegistrations FROM user WHERE created_at >= NOW() - INTERVAL 1 DAY";
$newRegistrationsStmt = $pdo->prepare($newRegistrationsQuery);
$newRegistrationsStmt->execute();
$newRegistrations = $newRegistrationsStmt->fetch(PDO::FETCH_ASSOC)['newRegistrations'];

$totalEarningsQuery = "SELECT SUM(amount) AS totalEarnings FROM payment WHERE date >= DATE_FORMAT(NOW() ,'%Y-%m-01')";
$totalEarningsStmt = $pdo->prepare($totalEarningsQuery);
$totalEarningsStmt->execute();
$totalEarnings = $totalEarningsStmt->fetch(PDO::FETCH_ASSOC)['totalEarnings'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/viewanalytics.css"> <!-- Link to CSS file -->
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="modern-dashboard">
    <div class="dashboard-header">
        <h1>Admin Analytics Dashboard</h1>
        <p>Welcome back, Admin! Here's a quick overview of the platform's performance today.</p>
    </div>

    <!-- Real-time Metrics Section -->
    <section class="metrics-grid">
        <div class="metric-card">
            <h3>Total Users</h3>
            <p class="metric-value live" id="total-users"><?php echo $totalUsers; ?></p>
            <small>+15 today</small>
        </div>
        <div class="metric-card">
            <h3>Active Users</h3>
            <p class="metric-value live" id="active-users"><?php echo $activeUsers; ?></p>
            <small>Live Updates</small>
        </div>
        <div class="metric-card">
            <h3>New Registrations</h3>
            <p class="metric-value" id="new-registrations"><?php echo $newRegistrations; ?></p>
            <small>Last 24 hours</small>
        </div>
        <div class="metric-card">
            <h3>Total Earnings</h3>
            <p class="metric-value" id="total-earnings">$<?php echo $totalEarnings; ?></p>
            <small>This Month</small>
        </div>
    </section>

    <!-- Interactive Charts -->
    <section class="charts-grid">
        <div class="chart-card">
            <h3>Monthly User Growth</h3>
            <canvas id="user-growth-chart"></canvas>
        </div>
        <div class="chart-card">
            <h3>Revenue Trends</h3>
            <canvas id="revenue-trend-chart"></canvas>
        </div>
    </section>

    <!-- Additional Insights -->
    <section class="insights-section">
        <div class="insight-card">
            <h3>Most Popular Subjects</h3>
            <ul id="popular-subjects">
                <li>Math</li>
                <li>Science</li>
                <li>English</li>
            </ul>
        </div>
        <div class="insight-card">
            <h3>Upcoming Events</h3>
            <p class="no-events">No scheduled events</p>
        </div>
    </section>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userCtx = document.getElementById('user-growth-chart').getContext('2d');
const revenueCtx = document.getElementById('revenue-trend-chart').getContext('2d');

const userChart = new Chart(userCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'User Growth',
            data: [50, 100, 150, 200, 250, 300],
            borderColor: '#6c63ff',
            backgroundColor: '#00BCD4',
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});

const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Monthly Revenue ($)',
            data: [1000, 1200, 1400, 1600, 1800, 2000],
            backgroundColor: '#ff6b6b',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});

function fetchLiveData() {
    fetch('<?php echo ROOT; ?>/api/admin/live-metrics')
        .then(res => res.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.totalUsers;
            document.getElementById('active-users').textContent = data.activeUsers;
            document.getElementById('new-registrations').textContent = data.newRegistrations;
            document.getElementById('total-earnings').textContent = `$${data.totalEarnings}`;
        });
}

setInterval(fetchLiveData, 10000); // Update every 10 seconds
fetchLiveData(); // Initial fetch

</script>
</body>
</html>
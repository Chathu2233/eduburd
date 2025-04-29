<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include database connection

// Fetch the logged-in admin's name
$adminId = $_SESSION['user_id']; // Assuming the admin's ID is stored in the session
$adminQuery = "SELECT first_name FROM user WHERE user_id = :adminId";
$adminStmt = $pdo->prepare($adminQuery);
$adminStmt->execute([':adminId' => $adminId]);
$adminName = $adminStmt->fetch(PDO::FETCH_ASSOC)['first_name'] ?? 'Admin';

// Get the current year
$currentYear = date('Y');

// Fetch data for the pie chart
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM user";
$totalTutorsQuery = "SELECT COUNT(*) AS total_tutors FROM user WHERE user_role = 'tutor'";
$totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM user WHERE user_role = 'student'";
$totalParentsQuery = "SELECT COUNT(*) AS total_parents FROM user WHERE user_role = 'parent'";

$totalUsers = $pdo->query($totalUsersQuery)->fetch(PDO::FETCH_ASSOC)['total_users'];
$totalTutors = $pdo->query($totalTutorsQuery)->fetch(PDO::FETCH_ASSOC)['total_tutors'];
$totalStudents = $pdo->query($totalStudentsQuery)->fetch(PDO::FETCH_ASSOC)['total_students'];
$totalParents = $pdo->query($totalParentsQuery)->fetch(PDO::FETCH_ASSOC)['total_parents'];

// Fetch revenue data from the beginning of the year
$revenueQuery = "
    SELECT 
        (SELECT SUM(amount) FROM payment WHERE date >= :startOfYear) AS total_received
";
$revenueStmt = $pdo->prepare($revenueQuery);
$revenueStmt->execute([':startOfYear' => "$currentYear-01-01"]);
$revenueData = $revenueStmt->fetch(PDO::FETCH_ASSOC);

$totalReceived = $revenueData['total_received'] ?? 0;
$totalRevenue = $totalReceived * 0.2; // Platform's commission (20% of total received)

// Fetch monthly revenue data using the `date` column in `payment`
$monthlyRevenueQuery = "
    SELECT DATE_FORMAT(date, '%b') AS month, SUM(amount) * 0.2 AS total_revenue
    FROM payment
    WHERE YEAR(date) = :currentYear
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY DATE_FORMAT(date, '%Y-%m') ASC
";
$monthlyRevenueStmt = $pdo->prepare($monthlyRevenueQuery);
$monthlyRevenueStmt->execute([':currentYear' => $currentYear]);
$monthlyRevenueData = $monthlyRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare all months (January to December)
$allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyRevenueData = array_column($monthlyRevenueData, 'total_revenue', 'month');

// Initialize months and revenues with default values
$months = [];
$revenues = [];
foreach ($allMonths as $month) {
    $months[] = $month;
    $revenues[] = $monthlyRevenueData[$month] ?? 0; // Use 0 if no revenue data for the month
}

// Fetch data for the number of tutors per course
$tutorsPerCourseQuery = "
    SELECT c.name AS course_name, COUNT(tc.tutor_course_id) AS tutor_count
    FROM course c
    LEFT JOIN tutor_course tc ON c.course_id = tc.course_id
    GROUP BY c.course_id
    ORDER BY c.name ASC
";
$tutorsPerCourseStmt = $pdo->query($tutorsPerCourseQuery);
$tutorsPerCourseData = $tutorsPerCourseStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/sidebaradmin.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/admindashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
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
            <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1>
            <p>Here's an overview of the platform's performance and analytics.</p>

            <!-- Revenue Overview -->
            <div class="revenue-overview">
                <div class="revenue-card">
                    <h3>Total Revenue</h3>
                    <p>LKR <?php echo number_format($totalRevenue, 2); ?></p>
                </div>
                <div class="revenue-card">
                    <h3>Total Received (From <?php echo $currentYear; ?>)</h3>
                    <p>LKR <?php echo number_format($totalReceived, 2); ?></p>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="analytics-row">
                <!-- Pie Chart -->
                <div class="analytics-chart">
                    <h2>User Distribution</h2>
                    <canvas id="userPieChart" width="300" height="300"></canvas>
                </div>

                <!-- Revenue Growth Chart -->
                <div class="analytics-chart">
                    <h2>Monthly Revenue Growth</h2>
                    <canvas id="revenueGrowthChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Tutors Per Grade Bar Graph -->
            <div class="analytics-chart">
                <h2>Tutors Per Course</h2>
                <canvas id="tutorsPerCourseChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>

    <script>
        // Data for the pie chart
        const pieData = {
            labels: ['Tutors', 'Students', 'Parents'],
            datasets: [{
                label: 'User Distribution',
                data: [<?php echo $totalTutors; ?>, <?php echo $totalStudents; ?>, <?php echo $totalParents; ?>],
                backgroundColor: ['#4CAF50', '#f44336', '#2196F3'],
                hoverOffset: 4
            }]
        };

        // Config for the pie chart
        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const total = <?php echo $totalUsers; ?>;
                                const value = tooltipItem.raw;
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };

        // Render the pie chart
        const userPieChart = new Chart(
            document.getElementById('userPieChart'),
            pieConfig
        );

        // Data for the revenue growth chart
        const revenueData = {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Revenue (LKR)',
                data: <?php echo json_encode($revenues); ?>,
                borderColor: '#6c63ff',
                backgroundColor: 'rgba(108, 99, 255, 0.2)',
                fill: true,
                tension: 0.4
            }]
        };

        // Config for the revenue growth chart
        const revenueConfig = {
            type: 'line',
            data: revenueData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `LKR ${tooltipItem.raw.toLocaleString()}`; // Format with LKR and commas
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Revenue (LKR)'
                        },
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return `LKR ${value.toLocaleString()}`; // Format y-axis ticks with LKR
                            }
                        }
                    }
                }
            }
        };

        // Render the revenue growth chart
        const revenueGrowthChart = new Chart(
            document.getElementById('revenueGrowthChart'),
            revenueConfig
        );

        // Data for the Tutors Per Grade Bar Graph
        const tutorsPerCourseData = {
            labels: <?php echo json_encode(array_column($tutorsPerCourseData, 'course_name')); ?>,
            datasets: [{
                label: 'Number of Tutors',
                data: <?php echo json_encode(array_map('intval', array_column($tutorsPerCourseData, 'tutor_count'))); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        };

        // Config for the Tutors Per Grade Bar Graph
        const tutorsPerCourseConfig = {
            type: 'bar',
            data: tutorsPerCourseData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Hide the legend
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Courses'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Tutors'
                        },
                        beginAtZero: true
                    }
                }
            }
        };

        // Render the Tutors Per Grade Bar Graph
        const tutorsPerCourseChart = new Chart(
            document.getElementById('tutorsPerCourseChart'),
            tutorsPerCourseConfig
        );
    </script>
</body>
</html>




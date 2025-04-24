<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include database connection

$progressData = [];
$assignmentCompletion = 0;
$monthlyAverageScore = 0;
$subjectPerformanceData = [];
$monthlyPerformanceData = [];

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student progress data
    $query = "
        SELECT 
            gc.grade_class_id, 
            c.name AS course_name, 
            a.title AS assignment_title, 
            a.description AS assignment_description, 
            a.deadline, 
            asub.grade AS submission_grade, 
            asub.marks AS submission_marks, 
            asub.comment AS submission_comment
        FROM grade_class gc
        INNER JOIN course c ON gc.course_id = c.course_id
        INNER JOIN assignment a ON gc.grade_class_id = a.grade_class_id
        LEFT JOIN assignment_submission asub ON a.assignment_id = asub.assignment_id
        WHERE gc.student_id = :student_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch assignment completion percentage
    $completionQuery = "
        SELECT 
            (COUNT(asub.assignment_id) / COUNT(a.assignment_id)) * 100 AS completion_percentage
        FROM grade_class gc
        INNER JOIN assignment a ON gc.grade_class_id = a.grade_class_id
        LEFT JOIN assignment_submission asub ON a.assignment_id = asub.assignment_id
        WHERE gc.student_id = :student_id
    ";
    $stmt = $pdo->prepare($completionQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $assignmentCompletion = round($result['completion_percentage'] ?? 0, 2);

    // Fetch monthly average score
    $averageScoreQuery = "
        SELECT 
            (SUM(asub.marks) / (COUNT(a.assignment_id) * 100)) * 100 AS average_score
        FROM grade_class gc
        INNER JOIN assignment a ON gc.grade_class_id = a.grade_class_id
        LEFT JOIN assignment_submission asub ON a.assignment_id = asub.assignment_id
        WHERE gc.student_id = :student_id
    ";
    $stmt = $pdo->prepare($averageScoreQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $monthlyAverageScore = round($result['average_score'] ?? 0, 2);

    // Fetch subject-wise performance
    $subjectPerformanceQuery = "
        SELECT 
            c.name AS course_name, 
            ROUND(AVG(asub.marks), 2) AS average_marks
        FROM grade_class gc
        INNER JOIN course c ON gc.course_id = c.course_id
        INNER JOIN assignment a ON gc.grade_class_id = a.grade_class_id
        LEFT JOIN assignment_submission asub ON a.assignment_id = asub.assignment_id
        WHERE gc.student_id = :student_id
        GROUP BY c.name
    ";
    $stmt = $pdo->prepare($subjectPerformanceQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $subjectPerformanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch monthly performance trend for each subject
    $monthlyPerformanceQuery = "
        SELECT 
            c.name AS course_name,
            MONTHNAME(asub.created_at) AS month, 
            ROUND(AVG(asub.marks), 2) AS average_marks
        FROM grade_class gc
        INNER JOIN course c ON gc.course_id = c.course_id
        INNER JOIN assignment a ON gc.grade_class_id = a.grade_class_id
        LEFT JOIN assignment_submission asub ON a.assignment_id = asub.assignment_id
        WHERE gc.student_id = :student_id
        GROUP BY c.name, MONTH(asub.created_at)
        ORDER BY c.name, MONTH(asub.created_at)
    ";
    $stmt = $pdo->prepare($monthlyPerformanceQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $monthlyPerformanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pass data to JavaScript
$subjectPerformanceJson = json_encode($subjectPerformanceData);
$monthlyPerformanceJson = json_encode($monthlyPerformanceData);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Report</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/progressreport.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .filter-container {
            display: flex;
            align-items: center;
            float: right;
            margin-right: 10px;
        }
        .filter-container label {
            margin-right: 5px;
            font-weight: bold;
        }
        .filter-container select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-container .search-icon {
            margin-left: 5px;
            font-size: 16px;
            color: #009688;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <h1>Student Progress Report</h1>
                    <p>Comprehensive MIS Reports with Performance Analytics</p>
                </div>

                <!-- Performance Overview Cards -->
                <div class="cards">
                    <div class="card">
                        <h3>Assignment Completion</h3>
                        <p><?php echo $assignmentCompletion; ?>%</p>
                    </div>
                    <div class="card">
                        <h3>Monthly Average Score</h3>
                        <p><?php echo $monthlyAverageScore; ?>%</p>
                    </div>
                </div>

                <!-- Chart Row -->
                <div class="chart-row">
                    <div class="chart-container">
                        <h3>Performance Trend</h3>
                        <canvas id="performanceTrendChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Subject-wise Performance</h3>
                        <canvas id="subjectWiseChart"></canvas>
                    </div>
                </div>

                <!-- MIS Report Section -->
                <div>
                    <h3 style="display: inline-block;">Detailed MIS Report</h3>
                    <div class="filter-container">
                        <label for="courseFilter">Search by Course:</label>
                        <select id="courseFilter">
                            <option value="">All Courses</option>
                            <option value="English">English</option>
                            <option value="Maths">Maths</option>
                            <option value="Science">Science</option>
                            <option value="Social Studies">Social Studies</option>
                        </select>
                        <span class="search-icon">&#128269;</span>
                    </div>

                    <!-- Table -->
                    <table class="table" id="misReportTable">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Assignment Title</th>
                                <th>Description</th>
                                <th>Marks</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($progressData)): ?>
                                <?php foreach ($progressData as $row): ?>
                                    <tr data-course="<?php echo htmlspecialchars($row['course_name']); ?>">
                                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['assignment_title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['assignment_description']); ?></td>
                                        <td><?php echo htmlspecialchars($row['submission_marks'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['submission_comment'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No data available for this student.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script>
        // Monthly Performance Data from PHP
        const monthlyPerformanceData = <?php echo $monthlyPerformanceJson; ?>;

        // Group data by subject
        const groupedData = {};
        monthlyPerformanceData.forEach(item => {
            const subject = item.course_name;
            if (!groupedData[subject]) {
                groupedData[subject] = { months: [], marks: [] };
            }
            groupedData[subject].months.push(item.month);
            groupedData[subject].marks.push(item.average_marks);
        });

        // Prepare datasets for the chart
        const datasets = Object.keys(groupedData).map(subject => ({
            label: subject,
            data: groupedData[subject].marks,
            borderColor: getRandomColor(),
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: false,
        }));

        // Generate random colors for each subject
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Chart: Performance Trend
        const performanceTrendCtx = document.getElementById('performanceTrendChart').getContext('2d');
        const performanceTrendChart = new Chart(performanceTrendCtx, {
            type: 'line',
            data: {
                labels: [...new Set(monthlyPerformanceData.map(item => item.month))], // Unique months
                datasets: datasets,
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                        },
                    },
                },
            },
        });

        // Subject-wise Performance Data from PHP
        const subjectPerformanceData = <?php echo $subjectPerformanceJson; ?>;

        // Prepare data for the chart
        const subjectLabels = subjectPerformanceData.map(item => item.course_name);
        const subjectMarks = subjectPerformanceData.map(item => item.average_marks);

        // Chart: Subject-wise Performance
        const subjectWiseCtx = document.getElementById('subjectWiseChart').getContext('2d');
        const subjectWiseChart = new Chart(subjectWiseCtx, {
            type: 'bar',
            data: {
                labels: subjectLabels,
                datasets: [{
                    label: 'Average Marks (%)',
                    data: subjectMarks,
                    backgroundColor: '#009688',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });

        // Filter functionality for the MIS Report table
        document.getElementById('courseFilter').addEventListener('change', function () {
            const selectedCourse = this.value.toLowerCase();
            const rows = document.querySelectorAll('#misReportTable tbody tr');

            rows.forEach(row => {
                const courseName = row.getAttribute('data-course').toLowerCase();
                if (selectedCourse === "" || courseName === selectedCourse) {
                    row.style.display = ""; // Show row
                } else {
                    row.style.display = "none"; // Hide row
                }
            });
        });
    </script>
</body>
</html>
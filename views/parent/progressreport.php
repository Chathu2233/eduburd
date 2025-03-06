<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Report</title>

    

    <link rel="stylesheet" href="../../assets/css/parent/progress report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
</head>
<body>

     <!-- Header -->
     <header>
        <?php include '../header_parent.php'; ?>
    </header>
       
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Student Progress Report</h1>
            <p>Comprehensive MIS Reports with Performance Analytics</p>
        </div>

        <!-- Performance Overview Cards -->
        <div class="cards">
            <div class="card">
                <h3>Attendance</h3>
                <p>95%</p>
            </div>
            <div class="card">
                <h3>Assignment Completion</h3>
                <p>87%</p>
            </div>
            <div class="card">
                <h3>Monthly Average Score</h3>
                <p>82%</p>
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
            <h3>Detailed MIS Report</h3>

            <!-- Filters -->
            <div class="filter-container">
                <input type="text" placeholder="Search by Subject or Month">
                <select>
                    <option value="all">All Subjects</option>
                    <option value="math">Math</option>
                    <option value="science">Science</option>
                    <option value="english">English</option>
                    <option value="history">History</option>
                </select>
                <button>Apply Filters</button>
            </div>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Subject</th>
                        <th>Score (%)</th>
                        <th>Attendance (%)</th>
                        <th>Assignment Completion (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>January</td>
                        <td>Math</td>
                        <td>85%</td>
                        <td>98%</td>
                        <td>90%</td>
                    </tr>
                    <tr>
                        <td>January</td>
                        <td>Science</td>
                        <td>88%</td>
                        <td>97%</td>
                        <td>92%</td>
                    </tr>
                    <tr>
                        <td>February</td>
                        <td>Math</td>
                        <td>80%</td>
                        <td>95%</td>
                        <td>85%</td>
                    </tr>
                </tbody>
            </table>

            <!-- Actions -->
            <div class="actions">
                <button class="download">Download as PDF</button>
                <button class="download">Download as Excel</button>
            </div>
        </div>
    </div>

   
     <!-- footer -->
     <?php include '../footer.php'; ?>
</body>
<script>
        // Chart: Performance Trend
        const performanceTrendCtx = document.getElementById('performanceTrendChart').getContext('2d');
        const performanceTrendChart = new Chart(performanceTrendCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May'],
                datasets: [{
                    label: 'Score (%)',
                    data: [75, 80, 85, 88, 92],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: { responsive: true }
        });

        // Chart: Subject-wise Performance
        const subjectWiseCtx = document.getElementById('subjectWiseChart').getContext('2d');
        const subjectWiseChart = new Chart(subjectWiseCtx, {
            type: 'bar',
            data: {
                labels: ['Math', 'Science', 'English', 'History'],
                datasets: [{
                    label: 'Score (%)',
                    data: [70, 85, 80, 90],
                    backgroundColor: ['#D3D3D3', '#708090', '#36454F', '#C0C0C0']
                }]
            },
            options: { responsive: true }
        });
    </script>
</html>

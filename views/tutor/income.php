<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch total income
try {
    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS total_income 
        FROM tutor_payments 
        WHERE tutor_id = :tutor_id
    ");
    $stmt->execute([':tutor_id' => $tutor_id]);
    $total_income = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    die("Error fetching total income: " . $e->getMessage());
}

// Fetch current month income
try {
    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS current_month_income 
        FROM tutor_payments 
        WHERE tutor_id = :tutor_id AND MONTH(released_at) = MONTH(CURRENT_DATE()) AND YEAR(released_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute([':tutor_id' => $tutor_id]);
    $current_month_income = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    die("Error fetching current month income: " . $e->getMessage());
}

// Fetch previous month income
try {
    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS previous_month_income 
        FROM tutor_payments 
        WHERE tutor_id = :tutor_id AND MONTH(released_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(released_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ");
    $stmt->execute([':tutor_id' => $tutor_id]);
    $previous_month_income = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    die("Error fetching previous month income: " . $e->getMessage());
}

// Fetch payment details
try {
    $stmt = $pdo->prepare("
        SELECT payment_id, amount, released_at 
        FROM tutor_payments 
        WHERE tutor_id = :tutor_id 
        ORDER BY released_at DESC
    ");
    $stmt->execute([':tutor_id' => $tutor_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching payment details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Income</title>
    <link rel="stylesheet" href="../../assets/css/tutor/income.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header>
    <?php include '../header_tutor.php'; ?>
</header>

<main class="main-content">
    <h1>Your Income</h1>

    <!-- Analytics Section -->
    <div class="analytics">
        <div class="card">
            <h2>Total Income</h2>
            <p>$<?= number_format($total_income, 2) ?></p>
        </div>
        <div class="card">
            <h2>Current Month Income</h2>
            <p>$<?= number_format($current_month_income, 2) ?></p>
        </div>
        <div class="card">
            <h2>Previous Month Income</h2>
            <p>$<?= number_format($previous_month_income, 2) ?></p>
        </div>
        <div class="card">
            <h2>Income Change</h2>
            <?php
            $income_change = $previous_month_income > 0 
                ? (($current_month_income - $previous_month_income) / $previous_month_income) * 100 
                : 0;
            ?>
            <p><?= number_format($income_change, 2) ?>%</p>
        </div>
    </div>

    <!-- Chart and Payment Details Section -->
    <div class="chart-and-details">
        <!-- Chart Section -->
        <div class="chart-container">
            <canvas id="incomeChart" width="400" height="200"></canvas>
        </div>

        <!-- Payment Details Section -->
        <div class="payment-details">
            <h2>Payment History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Released At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                            <td>$<?= number_format($payment['amount'], 2) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($payment['released_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
    // Chart.js for Income Comparison
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Previous Month', 'Current Month'],
            datasets: [{
                label: 'Income ($)',
                data: [<?= $previous_month_income ?>, <?= $current_month_income ?>],
                backgroundColor: ['#ff6384', '#36a2eb'],
                borderColor: ['#ff6384', '#36a2eb'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (context) => `$${context.raw.toFixed(2)}` } }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php include '../footer.php'; ?>

</body>
</html>
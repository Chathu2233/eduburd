<?php
session_start();
require_once '../constants.php';
require_once '../db.php'; // Include the database connection

// Get grade_class_id from the URL
$grade_class_id = isset($_GET['grade_class_id']) ? intval($_GET['grade_class_id']) : 0;

// Fetch payment history from the database
$payments = [];
if ($grade_class_id) {
    $stmt = $pdo->prepare("
        SELECT p.date, p.amount, p.method
        FROM payment p
        WHERE p.grade_class_id = :grade_class_id
    ");
    $stmt->execute(['grade_class_id' => $grade_class_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/paymenthistory.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div>
                <h2>Payment History</h2>
                <!-- Payment History Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payments)): ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['date']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['method']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No payment history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
<?php
// Database connection
include '../db.php';
include '../constants.php'; // Include the constants file

// Fetch total released payments
$releasedQuery = "
    SELECT 
        tp.tutor_id,
        COALESCE(SUM(tp.amount), 0) AS total_released
    FROM tutor_payments tp
    GROUP BY tp.tutor_id
";
$releasedStmt = $pdo->prepare($releasedQuery);
$releasedStmt->execute();
$releasedPayments = $releasedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total owed (total received by students and total owed to tutors)
$owedQuery = "
    SELECT 
        t.tutor_id,
        CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        COUNT(gc.grade_class_id) AS total_classes,
        SUM(p.amount) AS total_received,
        SUM(p.amount) * 0.8 AS total_owed
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    LEFT JOIN payment p ON gc.grade_class_id = p.grade_class_id
    GROUP BY t.tutor_id
";
$owedStmt = $pdo->prepare($owedQuery);
$owedStmt->execute();
$owedPayments = $owedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch detailed released payments
$detailedReleasedQuery = "
    SELECT 
        tp.payment_id,
        CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        tp.amount,
        tp.released_at
    FROM tutor_payments tp
    JOIN tutor t ON tp.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    ORDER BY tp.released_at DESC
";
$detailedReleasedStmt = $pdo->prepare($detailedReleasedQuery);
$detailedReleasedStmt->execute();
$detailedReleasedPayments = $detailedReleasedStmt->fetchAll(PDO::FETCH_ASSOC);

// Combine owed and released data
$tutorPayments = [];
foreach ($owedPayments as $owed) {
    $tutorId = $owed['tutor_id'];
    $totalReleased = 0;

    foreach ($releasedPayments as $released) {
        if ($released['tutor_id'] == $tutorId) {
            $totalReleased = $released['total_released'];
            break;
        }
    }

    $pendingAmount = $owed['total_owed'] - $totalReleased;

    $tutorPayments[] = [
        'tutor_id' => $tutorId,
        'tutor_name' => $owed['tutor_name'],
        'total_classes' => $owed['total_classes'],
        'total_received' => $owed['total_received'],
        'total_owed' => $owed['total_owed'],
        'total_released' => $totalReleased,
        'pending_amount' => $pendingAmount
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/managepayments.css">
</head>
<body>
<?php include '../header_admin.php'; ?>

<div class="manage-container">
    <h1>Manage Payments</h1>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <div class="payment-list">
        <h2>Pending Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Tutor Name</th>
                    <th>Total Classes</th>
                    <th>Total Received</th>
                    <th>Total Owed</th>
                    <th>Amount Released</th>
                    <th>Pending Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutorPayments as $payment): ?>
                    <?php if ($payment['pending_amount'] > 0): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['tutor_name']) ?></td>
                            <td><?= $payment['total_classes'] ?></td>
                            <td>$<?= number_format($payment['total_received'], 2) ?></td>
                            <td>$<?= number_format($payment['total_owed'], 2) ?></td>
                            <td>$<?= number_format($payment['total_released'], 2) ?></td>
                            <td>$<?= number_format($payment['pending_amount'], 2) ?></td>
                            <td>
                                <form method="GET" action="releasepayment.php">
                                    <input type="hidden" name="tutor_id" value="<?= $payment['tutor_id'] ?>">
                                    <input type="number" name="amount_to_pay" step="0.01" placeholder="Amount" required>
                                    <button type="submit">Release</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="released-payments">
        <h2>Released Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Tutor Name</th>
                    <th>Amount</th>
                    <th>Released At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($detailedReleasedPayments): ?>
                    <?php foreach ($detailedReleasedPayments as $payment): ?>
                        <tr>
                            <td><?= $payment['payment_id'] ?></td>
                            <td><?= htmlspecialchars($payment['tutor_name']) ?></td>
                            <td>$<?= number_format($payment['amount'], 2) ?></td>
                            <td><?= $payment['released_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No released payments.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>
</body>
</html>

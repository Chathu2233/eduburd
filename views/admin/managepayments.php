<?php
// Database connection
include '../db.php';
include '../constants.php'; // Include the constants file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutorId = $_POST['tutor_id'];
    $amountToPay = $_POST['amount_to_pay'];

    // Fetch the pending amount for the tutor
    $pendingQuery = "
        SELECT 
            (SUM(gc.duration) * 3 - COALESCE(SUM(tp.amount), 0)) AS pending_amount
        FROM grade_class gc
        JOIN tutor t ON gc.tutor_id = t.tutor_id
        LEFT JOIN tutor_payments tp ON t.tutor_id = tp.tutor_id
        WHERE t.tutor_id = :tutor_id
        GROUP BY t.tutor_id
    ";
    $stmt = $pdo->prepare($pendingQuery);
    $stmt->execute([':tutor_id' => $tutorId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $pendingAmount = $result['pending_amount'] ?? 0;

    // Check if the amount to pay exceeds the pending amount
    if ($amountToPay > $pendingAmount) {
        // Redirect back with an error message
        header("Location: managepayments.php?error=Amount exceeds pending amount for this tutor.");
        exit();
    }

    // Insert the released payment into the `tutor_payments` table
    $query = "INSERT INTO tutor_payments (tutor_id, amount, released_at) VALUES (:tutor_id, :amount, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':tutor_id' => $tutorId,
        ':amount' => $amountToPay
    ]);

    // Redirect back to the manage payments page with a success message
    header("Location: managepayments.php?success=Payment released successfully.");
    exit();
}

// Fetch total owed (classes, hours, and total owed)
$owedQuery = "
    SELECT 
        t.tutor_id,
        CONCAT(u.first_name, ' ', u.last_name) AS tutor_name,
        COUNT(gc.grade_class_id) AS total_classes,
        SUM(gc.duration) AS total_hours,
        SUM(gc.duration) * 3 AS total_owed, -- Total owed (hourly rate = $3)
        COALESCE(SUM(p.amount), 0) AS total_received -- Total amount received by the admin
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    LEFT JOIN payment p ON gc.grade_class_id = p.class_id
    GROUP BY t.tutor_id
";
$owedStmt = $pdo->prepare($owedQuery);
$owedStmt->execute();
$owedPayments = $owedStmt->fetchAll(PDO::FETCH_ASSOC);

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


// Combine results
$tutorPayments = [];
foreach ($owedPayments as $owed) {
    $tutorId = $owed['tutor_id'];
    $totalReleased = 0;

    // Find the released amount for this tutor
    foreach ($releasedPayments as $released) {
        if ($released['tutor_id'] == $tutorId) {
            $totalReleased = $released['total_released'];
            break;
        }
    }

    // Calculate pending amount
    $pendingAmount = $owed['total_owed'] - $totalReleased;

    // Add to final results
    $tutorPayments[] = [
        'tutor_id' => $tutorId,
        'tutor_name' => $owed['tutor_name'],
        'total_classes' => $owed['total_classes'],
        'total_hours' => $owed['total_hours'],
        'total_owed' => $owed['total_owed'],
        'total_received' => $owed['total_received'], // Add total received by admin
        'total_released' => $totalReleased,
        'pending_amount' => $pendingAmount
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/managepayments.css">
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="manage-container">
    <h1>Manage Payments</h1>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Pending Payments -->
    <div class="payment-list">
        <h2>Pending Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Tutor Name</th>
                    <th>Total Classes</th>
                    <th>Total Hours</th>
                    <th>Total Received</th> <!-- Updated Column -->
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
                            <td><?php echo htmlspecialchars($payment['tutor_name']); ?></td>
                            <td><?php echo htmlspecialchars($payment['total_classes']); ?></td>
                            <td><?php echo htmlspecialchars($payment['total_hours']); ?> hours</td>
                            <td>$<?php echo htmlspecialchars($payment['total_received']); ?></td> <!-- Display Total Received -->
                            <td>$<?php echo htmlspecialchars($payment['total_owed']); ?></td>
                            <td>$<?php echo htmlspecialchars($payment['total_released']); ?></td>
                            <td>$<?php echo htmlspecialchars($payment['pending_amount']); ?></td>
                            <td>
                                <form method="POST" action="managepayments.php">
                                    <input type="hidden" name="tutor_id" value="<?php echo htmlspecialchars($payment['tutor_id']); ?>">
                                    <input type="number" name="amount_to_pay" placeholder="Enter amount" required>
                                    <button type="submit" class="release-btn">Release Payment</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Released Payments -->
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
                <?php if (!empty($detailedReleasedPayments)): ?>
                    <?php foreach ($detailedReleasedPayments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                            <td><?php echo htmlspecialchars($payment['tutor_name']); ?></td>
                            <td>$<?php echo htmlspecialchars($payment['amount']); ?></td>
                            <td><?php echo htmlspecialchars($payment['released_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No released payments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>

</body>
</html>

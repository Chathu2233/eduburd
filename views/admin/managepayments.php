<?php
// Database connection
include '../db.php';

// Fetch payment data
$query = "SELECT * FROM payment";
$stmt = $pdo->prepare($query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin/managepayments.css">
</head>
<body>

<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>
<div class="manage-container">
    <h1>Manage Payments</h1>
    
    <!-- Payment List -->
    <div class="payment-list">
        <h2>Payment List</h2>
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
                <?php
                // Display each payment record
                foreach ($payments as $payment) {
                    echo "<tr>
                        <td>{$payment['payment_id']}</td>
                        <td>{$payment['class_id']}</td>
                        <td>{$payment['amount']}</td>
                        <td>{$payment['date']}</td>
                        <td>{$payment['method']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../footer.php'; ?>

</body>
</html>

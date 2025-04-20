<?php
session_start();
require_once '../constants.php';
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
            <div class="container">
                <h2>Payment History</h2>
                <!-- Payment History Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>20/11/2024</td>
                            <td>$100</td>
                            <td>Credit Card</td>
                            <td>Completed</td>
                            <td><button class="delete-btn" onclick="deletePayment(this)">Delete</button></td>
                        </tr>
                        <tr>
                            <td>22/11/2024</td>
                            <td>$150</td>
                            <td>PayPal</td>
                            <td>Pending</td>
                            <td><button class="delete-btn" onclick="deletePayment(this)">Delete</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script>
        // Function to delete a payment history entry
        function deletePayment(button) {
            const row = button.closest('tr');
            if (confirm('Are you sure you want to delete this payment history?')) {
                row.remove();
            }
        }
    </script>
</body>
</html>
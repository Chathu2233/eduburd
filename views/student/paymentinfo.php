<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="../../assets/css/student/paymentinfo.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Payment Content -->
        <main class="dashboard">
            <section class="payment-section">
                <h1>Student Payment History</h1>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Subject</th>
                            <th>Month</th>
                            <th>Date of Payment</th>
                            <th>Amount (USD)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mr. Smith</td>
                            <td>Mathematics</td>
                            <td>January</td>
                            <td>01/10/2024</td>
                            <td>$50.00</td>
                            <td class="status-paid">Paid</td>
                        </tr>
                        <tr>
                            <td>Ms. Johnson</td>
                            <td>Science</td>
                            <td>February</td>
                            <td>02/15/2024</td>
                            <td>$60.00</td>
                            <td class="status-paid">Paid</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
    <div class="content">
    <div class="container">
        <h1>Student Payment History</h1>

        <!-- Payment History Table -->
        <section class="payment-section">
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
                    <tr>
                        <td>Mr. Lee</td>
                        <td>History</td>
                        <td>March</td>
                        <td>Pending</td>
                        <td>$40.00</td>
                        <td class="status-pending">Pending</td>
                    </tr>
                    <tr>
                        <td>Ms. Davis</td>
                        <td>English</td>
                        <td>April</td>
                        <td>04/05/2024</td>
                        <td>$45.00</td>
                        <td class="status-paid">Paid</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    </div>
    <?php include '../footer.php'; ?>  
</body>
</html>

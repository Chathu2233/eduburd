<?php 
session_start();

require '../db.php'; // Include database connection

// Check if grade_class_id is passed and valid
if (!isset($_GET['student_id']) || empty($_GET['grade_class_id']) || !is_numeric($_GET['grade_class_id'])) {
    die("Invalid or missing grade_class_id.");
}

$grade_class_id = $_GET['grade_class_id'];

try {
    // Fetch payment details based on grade_class_id
    $stmt = $pdo->prepare("
        SELECT 
            payment_id,
            grade_class_id,
            amount,
            date,
            method
        FROM payment
        WHERE grade_class_id = :grade_class_id
    ");
    $stmt->execute([':grade_class_id' => $grade_class_id]);
    $payment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if no records are found
    if (empty($payment_list)) {
        $payment_list = []; // Ensure it's an empty array for the table rendering
    }

} catch (PDOException $e) {
    die("Error fetching payment data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="../../assets/css/student/paymentinfo.css">
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
            <section>
            <div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>

                <h1>Student payment history</h1>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payment_list)): ?>
                            <?php foreach ($payment_list as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['date']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['method']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No payment records found for this class.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
   
    
    <!-- Footer -->
    <?php include '../footer.php'; ?> 
</body>
</html>

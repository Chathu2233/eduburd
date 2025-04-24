<?php
include '../db.php';

// Get the base URL dynamically
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . "/eduburd";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $tutorId = $_GET['tutor_id'];
    $amountToPay = $_GET['amount_to_pay'];

    // Validate the amount to pay
    $pendingQuery = "
        SELECT 
            (SUM(p.amount) * 0.8 - COALESCE(SUM(tp.amount), 0)) AS pending_amount
        FROM payment p
        JOIN grade_class gc ON p.grade_class_id = gc.grade_class_id
        LEFT JOIN tutor_payments tp ON gc.tutor_id = tp.tutor_id
        WHERE gc.tutor_id = :tutor_id
        GROUP BY gc.tutor_id
    ";
    $stmt = $pdo->prepare($pendingQuery);
    $stmt->execute([':tutor_id' => $tutorId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $pendingAmount = $result['pending_amount'] ?? 0;

    if ($amountToPay > $pendingAmount) {
        // Redirect back with an error message
        header("Location: managepayments.php?error=Amount exceeds pending amount for this tutor.");
        exit();
    }
}

// Display error or success messages
if (isset($_GET['error']) || isset($_GET['success'])) {
    $message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : htmlspecialchars($_GET['success']);
    $alertClass = isset($_GET['error']) ? 'alert-danger' : 'alert-success';

    echo "<div id='alert-message' class='alert $alertClass'>$message</div>";

    // Clear the query parameters from the URL
    echo "<script>
        window.history.replaceState(null, null, window.location.pathname);
    </script>";
}
?>

<script>
    // Automatically hide the alert message after 5 seconds
    setTimeout(function() {
        const alertMessage = document.getElementById('alert-message');
        if (alertMessage) {
            alertMessage.classList.add('hidden'); // Add fade-out effect
            setTimeout(() => alertMessage.style.display = 'none', 500); // Wait for fade-out to complete
        }
    }, 5000); // 5000ms = 5 seconds
</script>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Release Payment</title>
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
</head>

<body>
    <div class="container">
        <h1>Release Payment</h1>
        <p>Processing payment for Tutor ID: <span id="tutorId"></span></p>
        <p>Amount: $<span id="amount"></span></p>
        <button id="payNowButton" class="btn btn-primary">Pay Now</button>
    </div>

    <script>
        const tutorId = "<?php echo htmlspecialchars($tutorId); ?>";
        const amountToPay = "<?php echo htmlspecialchars($amountToPay); ?>";

        document.getElementById("tutorId").innerText = tutorId;
        document.getElementById("amount").innerText = amountToPay;

        document.getElementById("payNowButton").addEventListener("click", function () {
            const payment = {
                sandbox: true,
                merchant_id: "1230206", // Your Merchant ID
                return_url: "<?php echo $baseUrl; ?>/views/admin/managepayments.php?success=Payment released successfully", // Redirect after success
                cancel_url: "<?php echo $baseUrl; ?>/views/admin/managepayments.php?error=Payment was canceled", // Redirect after cancellation
                notify_url: "<?php echo $baseUrl; ?>/notify", // Notification URL
                order_id: tutorId + "_" + new Date().getTime(), // Unique order ID
                items: "Tutor Payment",
                amount: parseFloat(amountToPay).toFixed(2), // Ensure amount is formatted to two decimal places
                currency: "USD", // Set currency to USD
                first_name: "Admin",
                last_name: "User",
                email: "admin@example.com",
                phone: "0771234567",
                address: "No.1, Galle Road",
                city: "Colombo",
                country: "Sri Lanka",
            };

            // Generate the hash for the payment request
    const merchantSecret = "MTQxNDU2OTE1NTQyMDc5MDk2Nzc3MzY0OTYxNTE0MTUyMzc5NTM4"; // Your Merchant Secret
    fetch("paymentprocess.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "tutor_id=" + tutorId + "&amount_to_pay=" + amountToPay
})
.then(response => response.json())
.then(data => {
    data.sandbox = true;

    // Set PayHere event handlers
    payhere.onCompleted = function(orderId) {
        console.log("Payment completed: " + orderId);
        window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?success=Payment released successfully";
    };

    payhere.onDismissed = function() {
        console.log("Payment dismissed");
        window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?error=Payment was canceled";
    };

    payhere.onError = function(error) {
        console.log("Error: " + error);
        window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?error=An error occurred during payment";
    };

    payhere.startPayment(data);
});


            // PayHere event handlers
            payhere.onCompleted = function onCompleted(orderId) {
                console.log("Payment completed. OrderID:" + orderId);

                // Insert the payment into the database
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "paymentprocess.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?success=Payment released successfully";
                    }
                };
                xhr.send("tutor_id=" + tutorId + "&amount_to_pay=" + amountToPay);
            };

            payhere.onDismissed = function onDismissed() {
                console.log("Payment dismissed");
                window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?error=Payment was canceled";
            };

            payhere.onError = function onError(error) {
                console.log("Error:" + error);
                window.location.href = "<?php echo $baseUrl; ?>/views/admin/managepayments.php?error=An error occurred during payment";
            };

            // Start the payment
            payhere.startPayment(payment);
        });

        // Helper function to generate MD5 hash
        function md5(string) {
            return CryptoJS.MD5(string).toString();
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</body>

</html>
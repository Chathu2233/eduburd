<?php
// filepath: c:\xampp\htdocs\eduburd\notify.php

// Log the notification for debugging
file_put_contents('notify_log.txt', print_r($_POST, true), FILE_APPEND);

// PayHere Merchant Secret
$merchant_secret = "MTQxNDU2OTE1NTQyMDc5MDk2Nzc3MzY0OTYxNTE0MTUyMzc5NTM4";

// Validate the notification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merchant_id = $_POST['merchant_id'] ?? '';
    $order_id = $_POST['order_id'] ?? '';
    $payhere_amount = $_POST['payhere_amount'] ?? '';
    $payhere_currency = $_POST['payhere_currency'] ?? '';
    $status_code = $_POST['status_code'] ?? '';
    $md5sig = $_POST['md5sig'] ?? '';

    // Generate the hash to validate the notification
    $generated_hash = strtoupper(
        md5(
            $merchant_id .
            $order_id .
            $payhere_amount .
            $payhere_currency .
            $status_code .
            strtoupper(md5($merchant_secret))
        )
    );

    // Check if the hash matches and the payment is successful
    if ($generated_hash === $md5sig && $status_code == "2") {
        // Payment is successful
        include 'db.php';

        // Extract tutor_id and amount from the order_id or other fields
        // Assuming order_id is in the format "tutorId_timestamp"
        $parts = explode('_', $order_id);
        $tutor_id = $parts[0] ?? null;
        $amount = $payhere_amount;

        if ($tutor_id && $amount) {
            // Insert the payment into the tutor_payments table
            $query = "INSERT INTO tutor_payments (tutor_id, amount, released_at) VALUES (:tutor_id, :amount, NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':tutor_id' => $tutor_id,
                ':amount' => $amount
            ]);
        }

        // Respond with a 200 OK
        http_response_code(200);
        echo "Payment recorded successfully.";
    } else {
        // Invalid notification
        http_response_code(400);
        echo "Invalid notification.";
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo "Method not allowed.";
}

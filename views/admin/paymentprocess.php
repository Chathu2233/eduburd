<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutorId = $_POST['tutor_id'];
    $amountToPay = $_POST['amount_to_pay'];

    $order_id = uniqid('ORDER_');
    $merchant_id = "1230206";
    $merchant_secret = "MTQxNDU2OTE1NTQyMDc5MDk2Nzc3MzY0OTYxNTE0MTUyMzc5NTM4";
    $currency = "USD";

    // Generate hash server-side
    $amountFormatted = number_format($amountToPay, 2, '.', '');
    $hash = strtoupper(
        md5(
            $merchant_id .
            $order_id .
            $amountFormatted .
            $currency .
            strtoupper(md5($merchant_secret))
        )
    );

    $payment = [
        "merchant_id" => $merchant_id,
        "return_url" => "http://localhost/eduburd/views/admin/managepayments.php?success=Payment released successfully",
        "cancel_url" => "http://localhost/eduburd/views/admin/managepayments.php?error=Payment was canceled",
        "notify_url" => "http://localhost/eduburd/notify",
        "order_id" => $order_id,
        "items" => "Tutor Payment",
        "amount" => $amountFormatted,
        "currency" => $currency,
        "hash" => $hash,
        "first_name" => "Admin",
        "last_name" => "User",
        "email" => "admin@example.com",
        "phone" => "0771234567",
        "address" => "No.1, Galle Road",
        "city" => "Colombo",
        "country" => "Sri Lanka"
    ];

    echo json_encode($payment);
    exit();
}

rom form
$tutorId = $_GET['tutor_id'];
$amountToPay = $_GET['amount_to_pay']; 

// Stripe expects amount in **cents** (so multiply by 100)
$amountInCents = $amountToPay * 100;

// Create a checkout session
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => "Payment to Tutor ID: $tutorId",
            ],
            'unit_amount' => $amountInCents,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => ROOT . '/views/admin/managepayments.php?success=Payment successful',
    'cancel_url' => ROOT . '/admin/managepayments.php?error=PaymentCancelled',
]);

// Redirect Admin to Stripe Checkout
header("Location: " . $checkout_session->url);
exit;
?>

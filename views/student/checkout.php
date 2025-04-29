
// Fetch tutor name and fee from the query parameters
$tutor_name = isset($_GET['tutor_name']) ? $_GET['tutor_name'] : 'Unknown Tutor';
$fee = isset($_GET['fee']) ? (int)$_GET['fee'] * 100 : 0; // Convert fee to cents
$grade_class_id = isset($_GET['grade_class_id']) ? $_GET['grade_class_id'] : 0;


// Create Stripe Checkout session
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $tutor_name,
            ],
            'unit_amount' => $fee ,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/eduburd/views/student/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/eduburd/views/student/cancel.php',
    'metadata' => [
        'grade_class_id' => $grade_class_id,
        'amount' => $fee
    ],
]);

http_response_code(303);
header("Location: " . $checkout_session->url);


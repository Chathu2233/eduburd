<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information</title>
    <link rel="stylesheet" href="../../assets/css/student/payment.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
    <div class="content">
    <!-- Main Content Section -->
    <section class="payment-section">
        <!-- Payment Info -->
        <div class="payment-info">
            <h1>Make Secure Payments Online</h1>
            <p>Experience fast, safe, and convenient transactions for all your needs.</p>
            <div class="payment-images">
                <img src="../../assets/images/paymentanim.gif" alt="Payment Animation">
                <img src="../../assets/images/online-payment.png" alt="Online Payment Illustration">
            </div>
        </div>
        
        <!-- Payment Icons -->
        <div class="payment-icons">
            <img src="../../assets/images/visa.png" alt="Visa">
            <img src="../../assets/images/mastercard.png" alt="MasterCard">
            <img src="../../assets/images/Apple Pay.png" alt="Apple Pay">
        </div>

        <!-- Payment Form -->
        <div class="payment-form-container">
            <form class="payment-form">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>

                <label for="card-number">Card Number</label>
                <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" required>

                <div class="flex-row">
                    <div>
                        <label for="expiry-month">Expiry Month</label>
                        <input type="text" id="expiry-month" name="expiry-month" placeholder="MM" required>
                    </div>
                    <div>
                        <label for="expiry-year">Expiry Year</label>
                        <input type="text" id="expiry-year" name="expiry-year" placeholder="YYYY" required>
                    </div>
                    <div>
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" required>
                    </div>
                </div>

                <label for="amount">Amount</label>
                <input type="text" id="amount" name="amount" placeholder="Enter amount" required>

                <label for="country">Country</label>
                <input type="text" id="country" name="country" placeholder="Enter your country" required>

                <button type="submit" class="submit-bttn">Submit Payment</button>
            </form>
        </div>
    </section>
    </div>
    <?php include '../footer.php'; ?>  
</body>
</html>

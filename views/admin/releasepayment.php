<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutorId = $_POST['tutor_id'];
    $amountToPay = $_POST['amount_to_pay'];

    // Insert the released payment into the `tutor_payments` table
    $query = "INSERT INTO tutor_payments (tutor_id, amount, released_at) VALUES (:tutor_id, :amount, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':tutor_id' => $tutorId,
        ':amount' => $amountToPay
    ]);

    // Redirect back to the manage payments page with a success message
    header("Location: managepayments.php?success=Payment released successfully.");
    exit();
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
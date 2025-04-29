$insertQuery);
    
    try {
        $stmt->execute([
            ':tutor_id' => $tutorId,
            ':amount' => $amount
        ]);
        
        // Redirect with success message
        header('Location: managepayments.php?success=Payment successfully recorded.');
        exit;
    } catch (PDOException $e) {
        // Redirect with error
        header('Location: managepayments.php?error=Failed to save payment.');
        exit;
    }
} else {
    // Redirect if no data
    header('Location: managepayments.php?error=Invalid payment data.');
    exit;
}
?>

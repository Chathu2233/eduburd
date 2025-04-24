<?php
session_start();
require 'db.php'; // Ensure the correct path to db.php

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Check if the verification code exists and is not already verified
    $stmt = $pdo->prepare("SELECT verification_code, is_verified FROM user WHERE verification_code = :code LIMIT 1");
    $stmt->bindParam(':code', $code);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['is_verified'] == 0) {
            // Update the verification status
            $updateStmt = $pdo->prepare("UPDATE user SET is_verified = 1, verification_code = NULL WHERE verification_code = :code LIMIT 1");
            $updateStmt->bindParam(':code', $code);
            $updateStmt->execute();

            if ($updateStmt->rowCount() > 0) {
                $_SESSION['status'] = "Your account has been verified successfully!";
                header("Location: login.php"); // Redirect to login page
                exit(0);
            } else {
                $_SESSION['status'] = "Verification failed. Please try again.";
                header("Location: login.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "This email has already been verified!";
            header("Location: login.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "Invalid or expired verification link.";
        header("Location: login.php");
        exit(0);
    }
} else {
    $_SESSION['status'] = "Access denied.";
    header("Location: login.php");
    exit(0);
}
?>

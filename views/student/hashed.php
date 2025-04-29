<?php
// The password to be hashed
$password = "Farshad@1234";

// Hash the password using the PASSWORD_DEFAULT algorithm
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo "Hashed Password: " . $hashedPassword;
?>

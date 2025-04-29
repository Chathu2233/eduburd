<?php
$password = "Farshad@123"; // Replace with your desired password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
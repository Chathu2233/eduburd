<?php
// Database configuration
$host = "localhost";  // Change this to your database host
$username = "root";   // Replace with your database username
$password = "";       // Replace with your database password
$dbname = "eduburd";

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
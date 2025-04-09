<?php 
define('DBHOST', 'localhost');
define('DBNAME', 'eduburd');
define('DBUSER', 'root');
define('DBPASS', '');

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
    
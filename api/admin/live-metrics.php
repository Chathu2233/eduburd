<?php
include '../../db.php';

header('Content-Type: application/json');

$totalUsersQuery = "SELECT COUNT(*) AS totalUsers FROM user";
$totalUsersStmt = $pdo->prepare($totalUsersQuery);
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['totalUsers'];

$activeUsersQuery = "SELECT COUNT(*) AS activeUsers FROM user WHERE last_activity >= NOW() - INTERVAL 1 DAY";
$activeUsersStmt = $pdo->prepare($activeUsersQuery);
$activeUsersStmt->execute();
$activeUsers = $activeUsersStmt->fetch(PDO::FETCH_ASSOC)['activeUsers'];

$newRegistrationsQuery = "SELECT COUNT(*) AS newRegistrations FROM user WHERE created_at >= NOW() - INTERVAL 1 DAY";
$newRegistrationsStmt = $pdo->prepare($newRegistrationsQuery);
$newRegistrationsStmt->execute();
$newRegistrations = $newRegistrationsStmt->fetch(PDO::FETCH_ASSOC)['newRegistrations'];

$totalEarningsQuery = "SELECT SUM(amount) AS totalEarnings FROM payment WHERE date >= DATE_FORMAT(NOW() ,'%Y-%m-01')";
$totalEarningsStmt = $pdo->prepare($totalEarningsQuery);
$totalEarningsStmt->execute();
$totalEarnings = $totalEarningsStmt->fetch(PDO::FETCH_ASSOC)['totalEarnings'];

echo json_encode([
    'totalUsers' => $totalUsers,
    'activeUsers' => $activeUsers,
    'newRegistrations' => $newRegistrations,
    'totalEarnings' => $totalEarnings
]);
?>
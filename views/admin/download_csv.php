<?php
include '../db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="analytics_report.csv"');

// Fetch data based on filters (optional)
$role = $_GET['role'] ?? null;
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "SELECT user_id, first_name, last_name, email, created_at FROM user WHERE 1=1";
$params = [];

if ($role) {
    $query .= " AND user_role = :role";
    $params['role'] = $role;
}
if ($startDate) {
    $query .= " AND created_at >= :start_date";
    $params['start_date'] = $startDate;
}
if ($endDate) {
    $query .= " AND created_at <= :end_date";
    $params['end_date'] = $endDate;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output CSV
$output = fopen('php://output', 'w');
fputcsv($output, ['User ID', 'First Name', 'Last Name', 'Email', 'Registration Date']); // Header row

foreach ($data as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();
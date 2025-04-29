<?php
// filepath: c:\xampp\htdocs\eduburd\views\admin\export_table.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = $_POST['table'] ?? 'data';
    $data = json_decode($_POST['data'], true);

    if (empty($data)) {
        die('No data available for export.');
    }

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $tableName . '_export.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers (column names)
    fputcsv($output, array_keys($data[0]));

    // Add rows to the CSV
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Close output stream
    fclose($output);
    exit();
}
?>
<?php
session_start();
include '../connect.php';

// Debugging: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Hardcoded student_id for testing purposes
$student_id = 11;

// Query to fetch parent requests for the logged-in student
$query = "SELECT psr.date, u.first_name, u.last_name, u.email, psr.status, psr.parent_student_request_id
          FROM parent_student_request psr
          JOIN parent p ON psr.parent_id = p.parent_id
          JOIN user u ON p.user_id = u.user_id
          WHERE psr.student_id = '$student_id'";

$result = mysqli_query($conn, $query);

// Debugging: Check if query executed successfully
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Debugging: Print query results
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row); // Debugging: Print each row
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/view_request.css">
</head>
<body>
<header>
    <?php include '../header_student.php'; ?>
</header>

<div class="container">
<div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>

    <h1>Parent Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Date of Request</th>
                <th>Parent Name</th>
                <th>Parent Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset result pointer for rendering table
            mysqli_data_seek($result, 0);

            if (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='5'>No requests found.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $actionBtns = ($row['status'] === "Pending") ? "
                        <button class='btn accept-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Accept\")'>Accept</button>
                        <button class='btn reject-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Reject\")'>Reject</button>" : "";

                    echo "<tr>
                        <td>{$row['date']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['status']}</td>
                        <td>$actionBtns</td>
                    </tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    <?php include '../footer.php'; ?>
</footer>

<script>
    function handleRequest(requestId, action) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                alert("Request " + action + "ed successfully!");
                location.reload();
            } else {
                alert("Error: " + xhr.responseText);
            }
        };

        xhr.send("request_id=" + requestId + "&action=" + action);
    }
</script>
</body>
</html>
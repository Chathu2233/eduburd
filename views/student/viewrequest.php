<?php
session_start();
require '../db.php'; // Use db.php for database connection

// Ensure the student is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];

try {
    // Get student_id using user_id
    $studentQuery = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $studentQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $studentQuery->execute();

    if ($studentQuery->rowCount() === 0) {
        die("Student not found.");
    }

    $studentData = $studentQuery->fetch(PDO::FETCH_ASSOC);
    $student_id = $studentData['student_id'];

    // Query to fetch parent requests for the logged-in student
    $query = $pdo->prepare("
        SELECT psr.date, u.first_name, u.last_name, u.email, psr.status, psr.parent_student_request_id
        FROM parent_student_request psr
        JOIN parent p ON psr.parent_id = p.parent_id
        JOIN user u ON p.user_id = u.user_id
        WHERE psr.student_id = :student_id
    ");
    $query->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $query->execute();

    $requests = $query->fetchAll(PDO::FETCH_ASSOC);

    // Handle POST requests to accept/reject
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $request_id = $_POST['request_id'];
        $action = $_POST['action'];

        if ($action === 'Accept') {
            // Update the status to 'Accepted'
            $updateQuery = $pdo->prepare("UPDATE parent_student_request SET status = 'Accepted' WHERE parent_student_request_id = :request_id");
            $updateQuery->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $updateQuery->execute();

            // Fetch parent_id and student_id for the accepted request
            $fetchQuery = $pdo->prepare("SELECT parent_id, student_id FROM parent_student_request WHERE parent_student_request_id = :request_id");
            $fetchQuery->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $fetchQuery->execute();

            if ($fetchQuery->rowCount() > 0) {
                $data = $fetchQuery->fetch(PDO::FETCH_ASSOC);
                $parent_id = $data['parent_id'];
                $student_id = $data['student_id'];

                // Insert into parent_student table
                $insertQuery = $pdo->prepare("INSERT INTO parent_student (parent_id, student_id) VALUES (:parent_id, :student_id)");
                $insertQuery->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
                $insertQuery->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $insertQuery->execute();
            }
        } elseif ($action === 'Reject') {
            // Update the status to 'Rejected'
            $updateQuery = $pdo->prepare("UPDATE parent_student_request SET status = 'Rejected' WHERE parent_student_request_id = :request_id");
            $updateQuery->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $updateQuery->execute();
        }

        echo "Success";
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/viewrequest.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
<header>
    <?php include '../header_student.php'; ?>
</header>

<div class="container">

<?php include 'sidebar.php'; ?>

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
            if (empty($requests)) {
                echo "<tr><td colspan='5'>No requests found.</td></tr>";
            } else {
                foreach ($requests as $row) {
                    $actionBtns = "";
                    if (strcasecmp(trim($row['status']), 'Pending') === 0) {
                        $actionBtns = "
                            <button class='btn accept-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Accept\")'>Accept</button>
                            <button class='btn reject-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Reject\")'>Reject</button>";
                    }

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
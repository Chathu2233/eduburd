<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];

    $query = "SELECT status FROM parent_student_request WHERE parent_student_request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status === 'Pending') {
        $deleteQuery = "DELETE FROM parent_student_request WHERE parent_student_request_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $request_id);

        if ($deleteStmt->execute()) {
            echo "Request deleted successfully.";
        } else {
            echo "Error deleting request.";
        }

        $deleteStmt->close();
    } else {
        echo "Request status is not 'Pending'; cannot delete.";
    }
} else {
    echo "Invalid request method.";
}
?>


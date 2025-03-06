<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $new_student_id = $_POST['new_student_id'];

    if (!empty($request_id) && !empty($new_student_id)) {
        $query = "UPDATE parent_student_request SET student_id = ? WHERE parent_student_request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_student_id, $request_id);

        if ($stmt->execute()) {
            header("Location: view_sent_requests.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid input.";
    }
} else {
    echo "Invalid request method.";
}
?>



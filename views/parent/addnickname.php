<?php
// addnickname.php
include '../connect.php';
$parent_id = 1;
$data = json_decode(file_get_contents("php://input"), true);
$student_id = $data['student_id'];
$nickname = $data['nickname'];

if ($student_id && $nickname) {
    $query = "UPDATE parent_student SET nick_name = ? WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nickname, $student_id);

    if ($stmt->execute()) {
        echo "Nickname added successfully.";
    } else {
        echo "Error adding nickname: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>

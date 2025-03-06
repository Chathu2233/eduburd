
  
<?php
// editnickname.php
include '../connect.php';
    $parent_id = 1;

$data = json_decode(file_get_contents("php://input"), true);
$student_id = $data['student_id'];
$new_nickname = $data['new_nickname'];

if ($student_id && $new_nickname) {
    $query = "UPDATE parent_student SET nick_name = ? WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_nickname, $student_id);

    if ($stmt->execute()) {
        echo "Nickname updated successfully.";
    } else {
        echo "Error updating nickname: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>

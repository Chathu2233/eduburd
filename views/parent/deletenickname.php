

<?php
// deletenickname.php
include '../connect.php'; // Your DB connection
$parent_id = 1;
$data = json_decode(file_get_contents("php://input"), true);
$student_id = $data['student_id'];

if ($student_id) {
    $query = "UPDATE parent_student SET nick_name = NULL WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        echo "Nickname deleted successfully.";
    } else {
        echo "Error deleting nickname: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>

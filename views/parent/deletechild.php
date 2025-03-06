
   
<?php
// deletechild.php
include '../connect.php';
$parent_id = 1;

$data = json_decode(file_get_contents("php://input"), true);
$student_id = $data['student_id'];

if ($student_id) {
    $query = "DELETE FROM parent_student WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        echo "Child deleted successfully.";
    } else {
        echo "Error deleting child: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>

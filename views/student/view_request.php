<?php
session_start();
include '../connect.php';

// Hardcoded student_id for testing
$student_id = 1; // Replace with the actual student ID from the session or login

// Handling Accept/Reject/Decline request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // Either 'Accept', 'Reject', or 'Decline'

    if ($action === "Accept") {
        $status = "Approved";

        // Fetch the parent_id linked to this request
        $fetch_parent_query = "SELECT parent_id FROM parent_student_request WHERE parent_student_request_id = '$request_id'";
        $result = mysqli_query($conn, $fetch_parent_query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $parent_id = $row['parent_id'];

            // Insert into the parent_student table
            $add_to_parent_student_query = "INSERT INTO parent_student (parent_id, student_id) VALUES ('$parent_id', '$student_id')";
            if (mysqli_query($conn, $add_to_parent_student_query)) {
                echo "Student added to parent_student table successfully.";
            } else {
                echo "Error adding student to parent_student: " . mysqli_error($conn);
                exit; // Stop further execution if there's an error
            }
        }

        // Update the status in the parent_student_request table
        $query = "UPDATE parent_student_request 
                  SET status = '$status' 
                  WHERE parent_student_request_id = '$request_id'";
        if (!mysqli_query($conn, $query)) {
            echo "Error updating request status: " . mysqli_error($conn);
            exit;
        }
    } elseif ($action === "Reject") {
        $status = "Rejected";
        $query = "UPDATE parent_student_request 
                  SET status = '$status' 
                  WHERE parent_student_request_id = '$request_id'";
        if (!mysqli_query($conn, $query)) {
            echo "Error updating request status: " . mysqli_error($conn);
            exit;
        }
    } elseif ($action === "Decline") {
        // Fetch the parent_id linked to this request
        $fetch_parent_query = "SELECT parent_id FROM parent_student_request WHERE parent_student_request_id = '$request_id'";
        $result = mysqli_query($conn, $fetch_parent_query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $parent_id = $row['parent_id'];

            // Delete the record from the parent_student table
            $delete_from_parent_student = "DELETE FROM parent_student WHERE parent_id = '$parent_id' AND student_id = '$student_id'";
            if (!mysqli_query($conn, $delete_from_parent_student)) {
                echo "Error deleting from parent_student table: " . mysqli_error($conn);
                exit;
            }
        }

        // Delete the record from the parent_student_request table
        $delete_from_request = "DELETE FROM parent_student_request WHERE parent_student_request_id = '$request_id'";
        if (!mysqli_query($conn, $delete_from_request)) {
            echo "Error deleting from parent_student_request table: " . mysqli_error($conn);
            exit;
        }
    }

    echo "Request successfully handled!";
    exit; // Stop further script execution
}

// Query to fetch parent requests for the logged-in student
$query = "SELECT psr.date, psr.parent_id, u.first_name, u.last_name, u.email, psr.status, psr.parent_student_request_id
          FROM parent_student_request psr
          JOIN user u ON psr.parent_id = u.user_id
          WHERE psr.student_id = '$student_id'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/view_request.css">
  
  
</head>


<!-- header -->
<header>
        <?php include '../header_student.php'; ?>
    </header>

    <div class="container">
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
        <!-- Example Rows -->
       
        <?php
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $statusClass = strtolower("status-" . $row['status']);
                $actionBtns = '';

                if ($row['status'] === "Pending") {
                    $actionBtns = "
                        <button class='btn accept-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Accept\")'>Accept</button>
                        <button class='btn reject-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Reject\")'>Reject</button>";
                } elseif ($row['status'] === "Approved") {
                    $actionBtns = "
                        <button class='btn decline-btn' onclick='handleRequest({$row['parent_student_request_id']}, \"Decline\")'>Decline</button>";
                }

                echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['first_name']} {$row['last_name']}</td>
                    <td>{$row['email']}</td>
                    <td class='$statusClass'>{$row['status']}</td>
                    <td>$actionBtns</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No requests found.</td></tr>";
        }
        ?>

    </tbody>
</table>
    </div>
        
    
<!-- Footer -->
<?php include '../footer.php'; ?>


</body>



<script>
    function handleRequest(requestId, action) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true); // Use the same page for handling requests
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                alert("Request " + action + "ed successfully!");
                location.reload(); // Reload the page to update the table
            } else {
                alert("Error: " + xhr.responseText);
            }
        };

        xhr.send("request_id=" + requestId + "&action=" + action);
    }
</script>
</html>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Requests</title>

    <link rel="stylesheet" href="../../assets/css/parent/view_sent_requests.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>

 <!-- Header -->
 <header>
        <?php include '../header_parent.php'; ?>
    </header>

<div class="content-container">
    <h1>Sent Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Date of Request Sent</th>
                <th>Student ID</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            
        <!-- Add more rows as needed -->
        <?php
        include '../connect.php';
       // $parent_id = 1;  // Replace with dynamic value from session

        $query = "SELECT date, student_id, status, parent_student_request_id
                  FROM parent_student_request ";
                //  JOIN student s ON psr.student_id = s.student_id
               //   JOIN user u ON s.user_id = u.user_id
                 // WHERE psr.parent_id = $parent_id";

        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Updated logic to handle all three statuses
                $statusClass = '';
                if ($row['status'] === 'Approved') {
                    $statusClass = 'status-approved';
                } elseif ($row['status'] === 'Pending') {
                    $statusClass = 'status-pending';
                } elseif ($row['status'] === 'Rejected') {
                    $statusClass = 'status-rejected';
                }
        
                echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['student_id']}</td>
                    <td class=\"$statusClass\">{$row['status']}</td>
                    <td>
                        <div class='actions'>
                            " . ($row['status'] === 'Pending' ? "
                            <button class='btn edit-btn' onclick='showEditForm({$row['parent_student_request_id']})'>
                                <i class='fas fa-pencil-alt'></i> <!-- Professional Pencil Icon -->
                            </button>
                            <button class='btn delete-btn' onclick='deleteRequest({$row['parent_student_request_id']})'>
                                <i class='fas fa-trash'></i>
                            </button>
                            " : "") . "
                        </div>
                       <form class='edit-form' id='edit-form-{$row['parent_student_request_id']}' action='editrequest.php' method='POST' style='display: none;'>
    <input type='hidden' name='request_id' value='{$row['parent_student_request_id']}'>
    <input type='hidden' name='current_student_id' value='{$row['student_id']}'>
    <input type='text' name='new_student_id' placeholder='New Student ID' required>
    <button type='submit'>Save</button>
</form>

                </tr>";
            }
        }
        
        ?>
    </tbody>
</table>
</div>
<script>
    function showEditForm(requestId) {
    // Toggle the visibility of the edit form
    const form = document.getElementById(`edit-form-${requestId}`);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
}


    function deleteRequest(requestId) {
        if (confirm("Are you sure you want to delete this request?")) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "deleterequest.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert("Request deleted successfully!");
                    location.reload();
                } else {
                    alert("Error deleting request: " + xhr.responseText);
                }
            };

            xhr.send("request_id=" + requestId);
        }
    }
</script>

 <!-- Footer -->
 <?php include '../footer.php'; ?>
</body>
</html>

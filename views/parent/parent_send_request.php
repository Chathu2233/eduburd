<?php session_start(); 
include '../connect.php'; 
require_once '../constants.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/parent_send_request.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/view_sent_requests.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar1_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Add Child Section -->
            <section class="announcement-section">
                <h2>Add Your Child</h2>
                <form method="POST" action="">
                    <label for="studentid">Student ID:</label>
                    <input type="text" name="studentid" id="studentid" placeholder="Enter Student ID" required>
                    <button type="submit">Send Request</button>
                </form>

                <!-- Messages -->
                <div class="error-message" style="display: none;">Error message here</div>
                <div class="success-message" style="display: none;">Success message here</div>
            </section>

            <!-- View Requests Section -->
            <section class="faq-section">
                <h2>Sent Requests</h2>
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
                        <tr>
                            <td>2025-03-07</td>
                            <td>12345</td>
                            <td class="status-pending">Pending</td>
                            <td>
                                <div class="actions">
                                    <button class="btn edit-btn" onclick="showEditForm(1)">
                                        <i class="fas fa-pencil-alt"></i> <!-- Professional Pencil Icon -->
                                    </button>
                                    <button class="btn delete-btn" onclick="deleteRequest(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form class="edit-form" id="edit-form-1" action="editrequest.php" method="POST" style="display: none;">
                                    <input type="hidden" name="request_id" value="1">
                                    <input type="hidden" name="current_student_id" value="12345">
                                    <input type="text" name="new_student_id" placeholder="New Student ID" required>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
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
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
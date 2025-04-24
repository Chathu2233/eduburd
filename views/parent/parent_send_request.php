<?php session_start(); 
include '../db.php'; 
require_once '../constants.php';

// Ensure parent_id is set in the session
if (!isset($_SESSION['parent_id'])) {
    // Fetch parent_id from the parent table using user_id
    $stmt = $pdo->prepare("SELECT parent_id FROM parent WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($parent) {
        $_SESSION['parent_id'] = $parent['parent_id'];
    } else {
        // Redirect to login or show an error message
        die("Parent ID not set in session. Please log in.");
    }
}

// Handle form submission for adding a request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['studentid'])) {
    $student_id = $_POST['studentid'];
    $parent_id = $_SESSION['parent_id']; // Assuming parent_id is stored in session

    // Validate student ID
    $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Insert request into parent_student_request table
        $stmt = $pdo->prepare("INSERT INTO parent_student_request (date, parent_id, student_id, status) VALUES (NOW(), :parent_id, :student_id, 'pending')");
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->bindParam(':student_id', $student_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Request sent successfully!";
        } else {
            $_SESSION['error_message'] = "Error sending request.";
            error_log("Error executing query: " . implode(":", $stmt->errorInfo()));
        }
    } else {
        $_SESSION['error_message'] = "Invalid student ID.";
        error_log("Student ID not found: " . $student_id);
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for editing a request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['new_student_id'])) {
    $request_id = $_POST['request_id'];
    $new_student_id = $_POST['new_student_id'];

    // Validate new student ID
    $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $new_student_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Update request in parent_student_request table
        $stmt = $pdo->prepare("UPDATE parent_student_request SET student_id = :student_id WHERE parent_student_request_id = :request_id");
        $stmt->bindParam(':student_id', $new_student_id);
        $stmt->bindParam(':request_id', $request_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Request updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating request.";
            error_log("Error executing query: " . implode(":", $stmt->errorInfo()));
        }
    } else {
        $_SESSION['error_message'] = "Invalid new student ID.";
        error_log("New student ID not found: " . $new_student_id);
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for deleting a request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_request_id'])) {
    $delete_request_id = $_POST['delete_request_id'];

    // Delete request from parent_student_request table
    $stmt = $pdo->prepare("DELETE FROM parent_student_request WHERE parent_student_request_id = :request_id");
    $stmt->bindParam(':request_id', $delete_request_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Request deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting request.";
        error_log("Error executing query: " . implode(":", $stmt->errorInfo()));
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch sent requests
$requests = [];
$stmt = $pdo->prepare("SELECT psr.date, psr.student_id, u.first_name, u.last_name, psr.status, psr.parent_student_request_id 
                       FROM parent_student_request psr 
                       JOIN student s ON psr.student_id = s.student_id 
                       JOIN user u ON s.user_id = u.user_id 
                       WHERE psr.parent_id = :parent_id");
$stmt->bindParam(':parent_id', $_SESSION['parent_id']);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $requests[] = $row;
}
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
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>
            </section>

            <!-- View Requests Section -->
            <section class="faq-section">
                <h2>Sent Requests</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date of Request Sent</th>
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo $request['date']; ?></td>
                            <td><?php echo $request['student_id']; ?></td>
                            <td><?php echo $request['first_name']; ?></td>
                            <td><?php echo $request['last_name']; ?></td>
                            <td class="status-<?php echo strtolower($request['status']); ?>"><?php echo ucfirst($request['status']); ?></td>
                            <td>
                                <div class="actions">
                                    <?php if (strcasecmp(trim($request['status']), 'Pending') === 0): ?>
                                        <button class="btn edit-btn" onclick="showEditForm(<?php echo $request['parent_student_request_id']; ?>)" style="font-size: 12px;">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </button>
                                        <button class="btn delete-btn" onclick="confirmDelete(<?php echo $request['parent_student_request_id']; ?>)" style="font-size: 12px;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <form class="edit-form" id="edit-form-<?php echo $request['parent_student_request_id']; ?>" action="" method="POST" style="display: none;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['parent_student_request_id']; ?>">
                                    <input type="hidden" name="current_student_id" value="<?php echo $request['student_id']; ?>">
                                    <input type="text" name="new_student_id" placeholder="New Student ID" required>
                                    <button type="submit">Save</button>
                                </form>
                                <form class="delete-form" id="delete-form-<?php echo $request['parent_student_request_id']; ?>" action="" method="POST" style="display: none;">
                                    <input type="hidden" name="delete_request_id" value="<?php echo $request['parent_student_request_id']; ?>">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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

        function confirmDelete(requestId) {
            if (confirm("Are you sure you want to delete this request?")) {
                document.getElementById(`delete-form-${requestId}`).submit();
            }
        }
    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
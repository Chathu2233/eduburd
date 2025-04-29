<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: ../login.php");
    exit();
}
$tutor_id = $_SESSION['tutor_id'];

// Fetch student requests for the logged-in tutor
try {
    $stmt = $pdo->prepare("
        SELECT 
            tsr.tutor_student_request_id, 
            tsr.student_id, 
            tsr.status, 
            tsr.date, 
            u.first_name, 
            u.last_name 
        FROM 
            tutor_student_request tsr
        JOIN 
            student s ON tsr.student_id = s.student_id
        JOIN 
            user u ON s.user_id = u.user_id
        WHERE 
            tsr.tutor_id = :tutor_id
        ORDER BY 
            tsr.date DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $student_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student requests: " . $e->getMessage());
}

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['accept', 'reject'])) {
        die("Invalid action.");
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE tutor_student_request 
            SET status = :status 
            WHERE tutor_student_request_id = :request_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':status' => $action === 'accept' ? 'accepted' : 'rejected',
            ':request_id' => $request_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Set success message
        $_SESSION['success_message'] = $action === 'accept' 
            ? "Student request accepted successfully!" 
            : "Student request rejected successfully!";

        // Redirect to avoid form resubmission
        header("Location: student_request.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating request status: " . $e->getMessage());
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request_id'])) {
    $delete_request_id = $_POST['delete_request_id'];

    try {
        $stmt = $pdo->prepare("
            DELETE FROM tutor_student_request 
            WHERE tutor_student_request_id = :request_id AND tutor_id = :tutor_id
        ");
        $stmt->execute([
            ':request_id' => $delete_request_id,
            ':tutor_id' => $tutor_id,
        ]);

        // Redirect to avoid form resubmission
        header("Location: student_request.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting request: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Requests</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/student_request.css">
</head>
<body>
<header>
    <?php include '../header_tutor.php'; ?>
</header>
<div class="container">
<?php include 'sidebar2.php'; ?> <!-- Include the sidebar -->

    <!-- Main Content Section -->
    <main>
        <section class="student-requests">
            <h2>📩 Student Requests</h2>
            <p>Manage incoming student requests by accepting or rejecting them.</p>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="modal" id="successModal" style="display: flex;">
                    <div class="modal-content">
                        <h2><?= htmlspecialchars($_SESSION['success_message']) ?></h2>
                        <button id="closeSuccessModal">OK</button>
                    </div>
                </div>
                <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
            <?php endif; ?>

            <table class="request-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>View Profile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student_requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                            <td><a href="view_student.php?student_id=<?= htmlspecialchars($request['student_id']) ?>" class="view-profile">View Profile</a></td>
                            <td>
                                <?php if ($request['status'] === 'pending'): ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['tutor_student_request_id']) ?>">
                                        <button type="submit" name="action" value="accept" class="btn accept-btn">Accept</button>
                                        <button type="submit" name="action" value="reject" class="btn delete-btn">Reject</button>
                                    </form>
                                <?php elseif ($request['status'] === 'accepted'): ?>
                                    <button class="btn accepted-btn" disabled>Accepted</button>
                                <?php elseif ($request['status'] === 'rejected'): ?>
                                    <button class="btn rejected-btn" disabled>Rejected</button>
                                <?php endif; ?>
                                <form method="POST" action="" onsubmit="return confirmDelete();" style="display: inline;">
                                    <input type="hidden" name="delete_request_id" value="<?= htmlspecialchars($request['tutor_student_request_id']) ?>">
                                    <button type="submit" class="delete-icon" title="Delete Request">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
 </div>

<?php include '../footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successModal = document.getElementById('successModal');
        const closeBtn = document.getElementById('closeSuccessModal');

        if (successModal) {
            closeBtn.addEventListener('click', function () {
                successModal.style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.onclick = function (event) {
                if (event.target === successModal) {
                    successModal.style.display = 'none';
                }
            };
        }
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this request?");
    }
</script>
</body>
</html>

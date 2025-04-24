<?php
session_start();
require '../db.php'; // Include database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Approve Request
if (isset($_POST['approve_request'])) {
    $request_id = $_POST['request_id'];

    // Fetch the tutor's email and other details
    $stmt = $pdo->prepare("
        SELECT u.email AS tutor_email, u.first_name, u.last_name 
        FROM tutor_admin_request r
        JOIN tutor t ON r.tutor_id = t.tutor_id
        JOIN user u ON t.user_id = u.user_id
        WHERE r.tutor_admin_request_id = :request_id
    ");
    $stmt->execute([':request_id' => $request_id]);
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tutor) {
        // Approve the request
        $stmt = $pdo->prepare("UPDATE tutor_admin_request SET status = 1 WHERE tutor_admin_request_id = :request_id");
        $stmt->execute([':request_id' => $request_id]);

        // Send login link to the tutor's email
        $login_link = "http://localhost/eduburd/views/login.php";
        $subject = "Your Tutor Account Has Been Approved";
        $message = "
            Dear {$tutor['first_name']} {$tutor['last_name']},\n\n
            Congratulations! Your tutor account has been approved.\n\n
            You can log in using the following link:\n
            $login_link\n\n
            Thank you for joining our platform!\n\n
            Best regards,\n
            EduBurd Admin Team
        ";
        $headers = "From: farshadfahumy@gmail.com\r\n";
        $headers .= "Reply-To: farshadfahumy@gmail.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($tutor['tutor_email'], $subject, $message, $headers)) {
            echo "<script>alert('Tutor request approved and login link sent.');</script>";
        } else {
            echo "<script>alert('Failed to send email. Please check your email configuration.');</script>";
        }
    }
}

// Handle Reject Request
if (isset($_POST['reject_request'])) {
    $request_id = $_POST['request_id'];

    // Reject the request
    $stmt = $pdo->prepare("UPDATE tutor_admin_request SET status = 2 WHERE tutor_admin_request_id = :request_id");
    $stmt->execute([':request_id' => $request_id]);

    echo "<script>alert('Tutor request rejected.');</script>";
}

// Fetch all pending tutor signup requests
$stmt = $pdo->prepare("
    SELECT 
        r.tutor_admin_request_id, 
        u.first_name, 
        u.last_name, 
        u.email, 
        u.contact_no AS phone, 
        t.cv AS cv_path 
    FROM tutor_admin_request r
    JOIN tutor t ON r.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    WHERE r.status = 0
    ORDER BY r.tutor_admin_request_id DESC
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Signup Requests</title>
    <link rel="stylesheet" href="../../assets/css/admin/sidebaradmin.css">
    <link rel="stylesheet" href="../../assets/css/admin/tutorsignuprequests.css">
</head>
<body>
    <header>
        <?php include '../header_admin.php'; ?>
    </header>

    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebaradmin.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Tutor Signup Requests</h1>

            <!-- Tutor Signup Requests Table -->
            <section class="table-section">
                <h2>Pending Requests</h2>
                <?php if (empty($requests)): ?>
                    <p>No pending tutor signup requests.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>CV</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    <td><?php echo htmlspecialchars($request['phone']); ?></td>
                                    <td>
                                        <?php if (!empty($request['cv_path'])): ?>
                                            <a href="../uploads/<?php echo htmlspecialchars($request['cv_path']); ?>" target="_blank">View CV</a>
                                        <?php else: ?>
                                            No CV uploaded
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Approve Button -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['tutor_admin_request_id']; ?>">
                                            <button type="submit" name="approve_request" class="btn approve-btn">Approve</button>
                                        </form>

                                        <!-- Reject Button -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['tutor_admin_request_id']; ?>">
                                            <button type="submit" name="reject_request" class="btn reject-btn">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
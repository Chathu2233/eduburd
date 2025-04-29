<?php
session_start();
require_once '../constants.php';
include '../connect.php';

if (!isset($conn) || !$conn) {
    die('Database connection error.');
}

// Get student_id from GET or SESSION
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $_SESSION['student_id'] = $student_id; // Optional: keep for future use
} elseif (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
} else {
    header('Location: ../parent/childlist.php');
    exit();
}

// Updated query to use the correct table name `student`
$query = "SELECT s.student_id, u.first_name, u.last_name, u.email, u.dob 
          FROM student s 
          JOIN user u ON s.user_id = u.user_id 
          WHERE s.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$child = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Profile</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/parentprofile.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <div class="main-layout">
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>

        <main class="main-content">
            <div class="profile-container1">
                <h2>Child Profile</h2>
                <img src="<?php echo ROOT; ?>/assets/images/dashboard.png" alt="Profile Picture">
                <div class="profile-details">
                    <div class="profile-box">
                        <p><strong>Student ID: </strong> <?php echo htmlspecialchars($child['student_id']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>First Name: </strong> <?php echo htmlspecialchars($child['first_name']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Last Name: </strong> <?php echo htmlspecialchars($child['last_name']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Email: </strong> <?php echo htmlspecialchars($child['email']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>DOB: </strong> <?php echo htmlspecialchars($child['dob']); ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>

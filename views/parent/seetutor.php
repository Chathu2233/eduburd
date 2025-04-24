<?php
session_start();
require_once '../constants.php';
require '../db.php';

// Ensure student_id, course_id, and tutor_id are set
if (isset($_GET['student_id']) && isset($_GET['course_id']) && isset($_GET['tutor_id'])) {
    $_SESSION['student_id'] = $_GET['student_id'];
    $_SESSION['course_id'] = $_GET['course_id'];
    $_SESSION['tutor_id'] = $_GET['tutor_id'];
} else {
    echo "Required parameters are missing.";
    exit();
}

$tutor_id = (int) $_SESSION['tutor_id'];

// Fetch tutor details
$query = "
    SELECT t.tutor_id, u.first_name AS tutor_first_name, u.last_name AS tutor_last_name, 
           u.email AS tutor_email, u.contact_no AS tutor_contact, t.years_of_experience, t.cv
    FROM tutor t
    JOIN user u ON t.user_id = u.user_id
    WHERE t.tutor_id = :tutor_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tutor) {
        echo "Tutor not found.";
        exit();
    }
} else {
    print_r($stmt->errorInfo());
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Profile</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/parentprofile.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="profile-container">
                <h2>Tutor Profile</h2>
                <img src="<?php echo ROOT; ?>/assets/images/studentpropic.png" alt="Profile Picture">
                <div class="profile-details">
                    <div class="profile-box">
                        <p><strong>First Name: </strong> <?php echo htmlspecialchars($tutor['tutor_first_name']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Last Name: </strong> <?php echo htmlspecialchars($tutor['tutor_last_name']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Contact Number: </strong> <?php echo htmlspecialchars($tutor['tutor_contact']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Email: </strong> <?php echo htmlspecialchars($tutor['tutor_email']); ?></p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Years of Experience: </strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                    </div>
                   
                </div>
            </div>
        </main>
    </div>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
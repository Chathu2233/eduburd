<?php
session_start();
require '../db.php'; // Include the database connection

// Ensure the student_id is provided in the URL
if (!isset($_GET['student_id'])) {
    die("Student ID not provided.");
}

$student_id = $_GET['student_id'];

// Fetch student data from the user and student tables
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.dob, 
            u.contact_no, 
            u.profile_photo
        FROM 
            user u
        JOIN 
            student s ON u.user_id = s.user_id
        WHERE 
            s.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found.");
    }
} catch (PDOException $e) {
    die("Error fetching student data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/view_student.css">
<body>
    <!-- Header Section -->
    <header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <!-- Student Profile Section -->
    <main class="profile-container">
        <section class="profile-card">
            <img src="../../<?= htmlspecialchars($student['profile_photo']) ?>" alt="Student Avatar" class="profile-avatar">
            <h2 class="student-name"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
            <p class="student-grade">Date of Birth: <?php echo htmlspecialchars($student['dob']); ?></p>
            <p class="student-email"><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p class="student-contact"><strong>Contact:</strong> <?php echo htmlspecialchars($student['contact_no']); ?></p>
            <div class="action-buttons">
                <a href="student_request.php" class="btn back-btn">Back</a>
            </div>
        </section>
    </main>
    <?php include '../footer.php'; ?>
</body>
</html>

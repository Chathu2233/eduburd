<?php
session_start();
require '../db.php'; // Include your database connection file

// Ensure the student is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];

try {
    // Get student_id using user_id
    $studentQuery = $pdo->prepare("SELECT student_id FROM student WHERE user_id = :user_id");
    $studentQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $studentQuery->execute();

    if ($studentQuery->rowCount() === 0) {
        die("Student not found.");
    }

    $studentData = $studentQuery->fetch(PDO::FETCH_ASSOC);
    $student_id = $studentData['student_id'];

    $parents = [];
    $query = "SELECT p.parent_id, u.first_name, u.last_name, u.email, u.contact_no, u.profile_photo 
              FROM parent_student ps
              JOIN parent p ON ps.parent_id = p.parent_id
              JOIN user u ON p.user_id = u.user_id
              WHERE ps.student_id = :student_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $parents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Requests</title>
    <link rel="stylesheet" href="../../assets/css/student/parentlist.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Parent Content -->
        <main class="dashboard">
            <section>
          

                <div class="parent-grid">
                    <?php if (empty($parents)): ?>
                        <p>No parents found for this student.</p>
                    <?php else: ?>
                        <?php foreach ($parents as $parent): ?>
                            <?php
                            $parentId = htmlspecialchars($parent['parent_id']);
                            $profilePhoto = $parent['profile_photo'];
                            $defaultImage = ROOT . "/assets/images/studentpropic.png"; // default image path
                            $displayImage = !empty($profilePhoto) ? ROOT . '/' . $profilePhoto : $defaultImage;
                            ?>
                            <div class="parent-box">
                                <div class="child-image">
                                    <img src="<?php echo $displayImage; ?>" alt="Parent Image">
                                </div>
                                <h3 class="parent-name"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></h3>
                                <a href="viewparent.php?parent_id=<?php echo $parentId; ?>" class="btn view-btn">View</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            <div class="back-button">
                    <button class="styled-back-button" onclick="history.back()">← Back</button>
                </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
</html>
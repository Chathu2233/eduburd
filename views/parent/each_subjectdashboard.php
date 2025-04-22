<?php
session_start();
require_once '../constants.php';
require '../db.php';

// Set the selected tutor_id in the session
if (isset($_GET['tutor_id'])) {
    $_SESSION['tutor_id'] = $_GET['tutor_id'];
}

// Check if student_id and course_id are passed in the URL
if (isset($_GET['student_id']) && isset($_GET['course_id'])) {
    $_SESSION['student_id'] = $_GET['student_id']; // Store student_id in session
    $course_id = $_GET['course_id']; // Get course_id from URL
} else {
    // Redirect to childlist.php if required parameters are missing
    header('Location: childlist.php');
    exit();
}

// Fetch subject name
$subject_query = "
    SELECT name
    FROM course
    WHERE course_id = :course_id
";
$subject_stmt = $pdo->prepare($subject_query);
$subject_stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$subject_stmt->execute();
$subject = $subject_stmt->fetch(PDO::FETCH_ASSOC);
$subject_name = $subject ? htmlspecialchars($subject['name']) : 'the subject';

// Fetch tutors for the specific subject and student
$query = "
    SELECT DISTINCT CONCAT(u.first_name, ' ', u.last_name) AS tutor_name, t.tutor_id, t.tutor_id
    FROM grade_class gc
    JOIN tutor t ON gc.tutor_id = t.tutor_id
    JOIN user u ON t.user_id = u.user_id
    WHERE gc.student_id = :student_id AND gc.course_id = :course_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Tutors</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/eachchild_dashboard.css">
    <style>
        .subject-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .subject-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        .subject-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%; /* Circular image */
            margin: 0 auto 15px; /* Center the image horizontally */
            display: block; /* Ensures the image is treated as a block element */
        }
        .subject-card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .subject-card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }
        .subject-card .view-details {
            display: inline-block;
            padding: 10px 20px;
            background-color: #009688;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .subject-card .view-details:hover {
            background-color: #00796b;
        }
    </style>
</head>
<body>
    <!--header-->
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout Container -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar3_parent.php'; ?>

        <!-- Main Content: Tutors for the Selected Subject -->
        <div class="container">
            <h1>Tutors for <?php echo $subject_name; ?></h1>
            <p align="center">Below are the tutors teaching this subject for your child.</p>
            <div class="subject-grid">
                <?php if (empty($tutors)): ?>
                    <p>No tutors found for this subject.</p>
                <?php else: ?>
                    <?php foreach ($tutors as $tutor): ?>
                        <?php
                            $tutorId = htmlspecialchars($tutor['tutor_id']);
                            $imageServerPath = __DIR__ . "/../uploads/tutors/tutor_$tutorId.jpg"; // server path
                            $imageWebPath = ROOT . "/uploads/tutors/tutor_$tutorId.jpg"; // public URL path
                            $defaultImage = ROOT . "/assets/images/studentpropic.png"; // default image path
                            $displayImage = file_exists($imageServerPath) ? $imageWebPath : $defaultImage;
                        ?>
                        <div class="subject-card">
                            <img src="<?php echo $displayImage; ?>" alt="<?php echo htmlspecialchars($tutor['tutor_name']); ?> Icon">
                            <h3><?php echo htmlspecialchars($tutor['tutor_name']); ?></h3>
                            <a href="seetutor.php?student_id=<?php echo $_SESSION['student_id']; ?>&course_id=<?php echo $course_id; ?>&tutor_id=<?php echo $tutor['tutor_id']; ?>" class="view-details">View Details</a>      </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
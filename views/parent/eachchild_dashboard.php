<?php
session_start();
require_once '../constants.php';
require '../db.php';

if (isset($_GET['student_id'])) {
    $_SESSION['student_id'] = $_GET['student_id'];
}

if (!isset($_SESSION['student_id'])) {
    header('Location: childlist.php');
    exit();
}

$student_id = $_SESSION['student_id'];

$name_query = "
    SELECT CONCAT(u.first_name, ' ', u.last_name) AS full_name
    FROM student s
    JOIN user u ON s.user_id = u.user_id
    WHERE s.student_id = :student_id
";
$name_stmt = $pdo->prepare($name_query);
$name_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$name_stmt->execute();
$student = $name_stmt->fetch(PDO::FETCH_ASSOC);
$student_name = $student ? htmlspecialchars($student['full_name']) : 'Your child';

$query = "
    SELECT DISTINCT gc.grade_class_id, c.course_id, c.name AS course_name, c.description AS course_description
    FROM grade_class gc
    JOIN course c ON gc.course_id = c.course_id
    WHERE gc.student_id = :student_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Each Child Dashboard</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/eachchild_dashboard.css?v=<?php echo time(); ?>">
    
</head>
<body>
<header>
    <?php include __DIR__ . '/../header_parent.php'; ?>
</header>

<div class="main-layout">
    <?php include __DIR__ . '/sidebar2_parent.php'; ?>

    <div class="container">
        <h1><?php echo $student_name; ?>'s enrolled subjects</h1>
        <p align="center">As a parent, you can review the subjects your child has enrolled in for the academic year.</p>
        <div class="subject-grid">
            <?php if (empty($subjects)): ?>
                <p>No subjects found for this student.</p>
            <?php else: ?>
                <?php foreach ($subjects as $subject): ?>
                    <?php
                        $courseName = strtolower(trim($subject['course_name']));
                        $imgName = 'common.jpeg'; // default

                        if (strpos($courseName, 'english') !== false) {
                            $imgName = 'eng.jpg';
                        } elseif (strpos($courseName, 'math') !== false) {
                            $imgName = 'math.jpeg';
                        } elseif (strpos($courseName, 'science') !== false) {
                            $imgName = 'sci.jpeg';
                        } elseif (strpos($courseName, 'social') !== false) {
                            $imgName = 'social.jpeg';
                        }

                        $imagePath = ROOT . "/assets/images/" . $imgName;
                    ?>
                    <div class="subject-card">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($subject['course_name']); ?> Image">
                        <div class="subject-card-content">
                            <h3><?php echo htmlspecialchars($subject['course_name']); ?></h3>
                            <p><?php echo htmlspecialchars($subject['course_description']); ?></p>
                            <a href="each_subjectdashboard.php?grade_class_id=<?php echo $subject['grade_class_id']; ?>&student_id=<?php echo $student_id; ?>&course_id=<?php echo $subject['course_id']; ?>" class="view-details">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
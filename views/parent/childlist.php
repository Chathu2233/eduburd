<?php
require '../db.php';
require_once '../constants.php';
session_start();

$parent_id = $_SESSION['parent_id'];

$children = [];
$query = "SELECT ps.student_id, u.first_name, u.last_name 
          FROM parent_student ps
          JOIN student s ON ps.student_id = s.student_id
          JOIN user u ON s.user_id = u.user_id
          WHERE ps.parent_id = :parent_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
$stmt->execute();
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student_id'])) {
    $student_id = $_POST['delete_student_id'];

    $deleteQuery = "DELETE FROM parent_student WHERE student_id = :student_id AND parent_id = :parent_id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Child deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting child.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Child List</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/mychildlist.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
<header>
    <?php include __DIR__ . '/../header_parent.php'; ?>
</header>

<div class="main-layout">
    <?php include __DIR__ . '/sidebar1_parent.php'; ?>

    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Children overview</h1>
            <div class="child-grid">
                <?php if (empty($children)): ?>
                    <p>No children found. Please add a child.</p>
                <?php else: ?>
                    <?php foreach ($children as $child): ?>
                        <?php
                            $studentId = htmlspecialchars($child['student_id']);
                            $imageServerPath = __DIR__ . "/../uploads/students/student_$studentId.jpg"; // server path
                            $imageWebPath = ROOT . "/uploads/students/student_$studentId.jpg"; // public URL path
                            $defaultImage = ROOT . "/assets/images/studentpropic.png"; // default image path
                            $displayImage = file_exists($imageServerPath) ? $imageWebPath : $defaultImage;
                        ?>
                        <div class="child-box" id="child-card-<?php echo $studentId; ?>">
                            <div class="child-image">
                                <img src="<?php echo $displayImage; ?>" alt="Child Image" class="student-img" style="width: 100px; height: 100px; border-radius: 50%;">
                            </div>
                            <div class="child-info">
                                <h3 class="child-name"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h3>
                            </div>
                            <div class="child-actions">
    <a href="eachchild_dashboard.php?student_id=<?php echo $studentId; ?>" class="btn view-btn">View</a>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this child?');">
        <input type="hidden" name="delete_student_id" value="<?php echo $studentId; ?>">
        <button type="submit" class="btn delete-btn">Delete</button>
    </form>
</div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="parent_send_request.php" class="add-child-btn">Add Child</a>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>

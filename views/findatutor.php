<?php
session_start();
require_once 'constants.php';
require 'db.php'; // Include your DB connection

// Default profile image path
$defaultImage = ROOT . "/assets/images/studentpropic.png";

// Handle the "Send Request" functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['tutor_id'])) {
    $student_id = $_POST['student_id'];
    $tutor_id = $_POST['tutor_id'];
    $status = 'pending';
    $date = date('Y-m-d');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO tutor_student_request (student_id, tutor_id, status, date)
            VALUES (:student_id, :tutor_id, :status, :date)
        ");
        $stmt->execute([
            ':student_id' => $student_id,
            ':tutor_id' => $tutor_id,
            ':status' => $status,
            ':date' => $date,
        ]);

        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Fetch data from the tutor table with filters
$filters = [];
$params = [];

// Filter by years of experience
if (isset($_GET['experience']) && is_numeric($_GET['experience'])) {
    $filters[] = "t.years_of_experience = :experience";
    $params[':experience'] = (int)$_GET['experience'];
}

$sql = "
    SELECT 
        t.tutor_id,
        u.first_name, 
        u.last_name, 
        u.profile_photo,
        t.years_of_experience, 
        t.description
    FROM tutor t
    JOIN user u ON t.user_id = u.user_id
";

if (!empty($filters)) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}

$sql .= " ORDER BY t.years_of_experience ASC"; 

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tutors: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Find a Tutor</title>
    <link rel="stylesheet" href="../assets/css/findatutor.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <?php
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include 'header_admin.php';
                    break;
                case 'student':
                    include 'header_student.php';
                    break;
                case 'tutor':
                    include 'header_tutor.php';
                    break;
                case 'parent':
                    include 'header_parent.php';
                    break;
                default:
                    include 'header_guest.php';
            }
        } else {
            include 'header_guest.php';
        }
        ?>
    </header>

    <!-- Content Section -->
    <div class="content-wrapper">
        <!-- Page Breadcrumb -->
        <div class="breadcrumb">
            <p>Homepage &gt; Find a tutor </p>
        </div>

        

        <!-- Main Content -->
        <div class="container">
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <h2>Filter Tutors</h2>
                <form method="GET" action="findatutor.php">
                    <div class="filter">
                        <label for="experience">Years of experience</label>
                        <input type="number" id="experience" name="experience" min="0" placeholder="Enter years of experience" value="<?php echo htmlspecialchars($_GET['experience'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="filter-btn1" style="background-color:#009688;">Apply Filters</button>
                </form>
            </aside>

            <!-- Tutor List -->
            
<main class="tutor-list">
    <?php if (empty($tutors)): ?>
        <p>No tutors found.</p>
    <?php else: ?>
        <?php foreach ($tutors as $tutor): ?>
            <div class="tutor">
                <div class="tutor-info">
                    <img 
                        src="<?php echo !empty($tutor['profile_photo']) ? ROOT .'/'  . htmlspecialchars($tutor['profile_photo']) : $defaultImage; ?>" 
                        alt="Tutor Profile" 
                        class="tutor-profile-photo"
                    >
                    <h3><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h3>
                        <p class="center-align"><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>
                  
                            <!-- Send Request Button -->
                            <button 
    class="send-request-btn" 
    data-tutor-id="<?php echo htmlspecialchars($tutor['tutor_id']); ?>">
    View profile
</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
        </div>
    </div>

    <script>
document.querySelectorAll('.send-request-btn').forEach(button => {
    button.addEventListener('click', () => {
        const tutorId = button.getAttribute('data-tutor-id');
        // Redirect to the tutor's profile page
        window.location.href = `viewteacher.php?tutor_id=${tutorId}`;
    });
});
</script>
</body>
</html>
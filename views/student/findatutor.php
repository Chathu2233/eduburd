<?php
session_start();
require_once '../constants.php';
require '../db.php'; 


$defaultImage = ROOT . "/assets/images/studentpropic.png";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['tutor_id'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
        echo json_encode(['success' => false, 'message' => 'You must be logged in as a student to send a request.']);
        exit;
    }

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


$filters = [];
$params = [];


if (!empty($_GET['course']) && $_GET['course'] !== 'all') {
    $filters[] = "t.course = :course";
    $params[':course'] = $_GET['course'];
}


if (!empty($_GET['experience'])) {
    $filters[] = "t.years_of_experience >= :experience";
    $params[':experience'] = $_GET['experience'];
}


if (!empty($_GET['level']) && $_GET['level'] !== 'All Levels') {
    $filters[] = "t.education_level = :level";
    $params[':level'] = $_GET['level'];
}


$sql = "
    SELECT 
        t.tutor_id,
        u.first_name, 
        u.last_name, 
        u.profile_photo,
        t.years_of_experience, 
        t.description,
        t.course,
        t.education_level
    FROM tutor t
    JOIN user u ON t.user_id = u.user_id
";

if (!empty($filters)) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            t.tutor_id,
            u.first_name, 
            u.last_name, 
            u.profile_photo, -- Fetch profile photo
            t.years_of_experience, 
            t.description
        FROM tutor t
        JOIN user u ON t.user_id = u.user_id
    ");
    $stmt->execute();
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
    <link rel="stylesheet" href="../../assets/css/student/findatutor.css">
</head>
<body>

    
    <header>
        <?php
        
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include 'header_admin.php';
                    break;
                case 'student':
                    include '../header_student.php';
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


    <div class="content-wrapper">
        
        <div class="breadcrumb">
            <p>Homepage &gt; Find a tutor</p>
        </div>

        
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a tutor..." onkeyup="filterTutorsByName()">
            <button class="search-btn" onclick="filterTutorsByName()">🔍</button>
        </div>

        
        <div class="container">
            
            <main class="tutor-list">
                <?php if (empty($tutors)): ?>
                    <p>No tutors found.</p>
                <?php else: ?>
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="tutor">
                        
                            <div class="tutor-photo">
                            <img src="../../<?= htmlspecialchars($tutor['profile_photo'] ?: 'assets/images/studentpropic.png') ?>" alt="Profile Image">
                            </div>

                            
                            <div class="tutor-info">
                                <h3><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h3>
                                <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>

                                
                                <a 
                                    href="viewteacher.php?tutor_id=<?php echo htmlspecialchars($tutor['tutor_id']); ?>" 
                                    class="view-profile-btn">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>


    <?php include  '../footer.php'; ?>

    <script>
        
        function filterTutorsByName() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tutors = document.querySelectorAll('.tutor');

            tutors.forEach(tutor => {
                const tutorName = tutor.querySelector('h3').textContent.toLowerCase();
                if (tutorName.includes(searchInput)) {
                    tutor.style.display = 'flex'; 
                } else {
                    tutor.style.display = 'none'; 
                }
            });
        }

        
        document.querySelectorAll('.send-request-btn').forEach(button => {
            button.addEventListener('click', () => {
                const studentId = button.getAttribute('data-student-id');
                const tutorId = button.getAttribute('data-tutor-id');

                if (!studentId || !tutorId) {
                    alert('Invalid student or tutor ID.');
                    return;
                }

               
                fetch('findatutor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        student_id: studentId,
                        tutor_id: tutorId,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Request sent successfully!');
                        button.disabled = true; 
                        button.textContent = 'Request Sent';
                    } else {
                        alert('Failed to send request: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the request.');
                });
            });
        });
    </script>
</body>
</html>
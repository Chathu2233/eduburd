<?php
session_start();
require_once 'constants.php';
require 'db.php'; // Include your DB connection

// Default profile image path
$defaultImage = ROOT . "/assets/images/studentpropic.png";

// Handle the "Send Request" functionality
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

// Fetch data from the tutor table with filters
$filters = [];
$params = [];

// Filter by course
if (!empty($_GET['course']) && $_GET['course'] !== 'all') {
    $filters[] = "t.course = :course";
    $params[':course'] = $_GET['course'];
}

// Filter by years of experience
if (!empty($_GET['experience'])) {
    $filters[] = "t.years_of_experience >= :experience";
    $params[':experience'] = $_GET['experience'];
}

// Filter by education level
if (!empty($_GET['level']) && $_GET['level'] !== 'All Levels') {
    $filters[] = "t.education_level = :level";
    $params[':level'] = $_GET['level'];
}

// Build the SQL query with filters
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
<<<<<<< HEAD
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
=======
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
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
<<<<<<< HEAD
        // Dynamically include the correct header based on user role
=======
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
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
<<<<<<< HEAD
                    include 'header_guest.php'; // Fallback for unknown roles
            }
        } else {
            include 'header_guest.php'; // For guests (not logged in)
=======
                    include 'header_guest.php';
            }
        } else {
            include 'header_guest.php';
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
        }
        ?>
    </header>

    <!-- Content Section -->
    <div class="content-wrapper">
        <!-- Page Breadcrumb -->
        <div class="breadcrumb">
<<<<<<< HEAD
            <p>Homepage &gt; Find a tutor</p>
=======
            <p>Homepage &gt; Find a tutor </p>
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
<<<<<<< HEAD
            <input type="text" id="searchInput" placeholder="Search for a tutor..." onkeyup="filterTutorsByName()">
            <button class="search-btn" onclick="filterTutorsByName()">🔍</button>
=======
            <form method="GET" action="findatutor.php">
                <select name="course">
                    <option value="all">All Courses</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="English">English</option>
                    <option value="Social studies">Social Studies</option>
                </select>
                <button type="submit" class="search-btn">🔍</button>
            </form>
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
        </div>

        <!-- Main Content -->
        <div class="container">
<<<<<<< HEAD
=======
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <h2>Filter Tutors</h2>
                <form method="GET" action="findatutor.php">
                    <div class="filter">
                        <label for="experience">Years of experience</label>
                        <input type="number" id="experience" name="experience" min="0" placeholder="Enter years of experience">
                    </div>
                    <div class="filter">
                        <label for="level">Education Level</label>
                        <select id="level" name="level">
                            <option>All Levels</option>
                            <option>Primary</option>
                            <option>Secondary</option>
                            <option>IGCSE</option>
                            <option>AS & A2</option>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </aside>

>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
            <!-- Tutor List -->
            <main class="tutor-list">
                <?php if (empty($tutors)): ?>
                    <p>No tutors found.</p>
                <?php else: ?>
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="tutor">
<<<<<<< HEAD
                            <!-- Tutor Profile Photo -->
                            <div class="tutor-photo">
                                <img src="<?php echo htmlspecialchars($tutor['profile_photo'] ?: 'https://via.placeholder.com/150'); ?>" alt="Profile Photo">
                            </div>

                            <!-- Tutor Information -->
                            <div class="tutor-info">
                                <h3><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h3>
                                <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>

                                <!-- View Profile Button -->
                                <a 
                                    href="viewteacher.php?tutor_id=<?php echo htmlspecialchars($tutor['tutor_id']); ?>" 
                                    class="view-profile-btn">
                                    View Profile
                                </a>
=======
                            <div class="tutor-info">
                                <img 
                                    src="<?php echo htmlspecialchars($tutor['profile_photo'] ?: $defaultImage); ?>" 
                                    alt="Tutor Profile" 
                                    class="tutor-profile-photo" style="width: 100px; height: 100px; border-radius: 50%;"
                                >
                                <h3><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h3>
                                <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                                <p><strong>Course:</strong> <?php echo htmlspecialchars($tutor['course']); ?></p>
                                <p><strong>Education Level:</strong> <?php echo htmlspecialchars($tutor['education_level']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>

                                <!-- Send Request Button -->
                                <button 
                                    class="send-request-btn" 
                                    data-student-id="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : ''; ?>" 
                                    data-tutor-id="<?php echo htmlspecialchars($tutor['tutor_id']); ?>">
                                    Send Request
                                </button>
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Footer Section -->
    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        // Function to filter tutors by name
        function filterTutorsByName() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tutors = document.querySelectorAll('.tutor');

            tutors.forEach(tutor => {
                const tutorName = tutor.querySelector('h3').textContent.toLowerCase();
                if (tutorName.includes(searchInput)) {
                    tutor.style.display = 'flex'; // Show matching tutors
                } else {
                    tutor.style.display = 'none'; // Hide non-matching tutors
                }
            });
        }

        // Handle "Send Request" functionality
        document.querySelectorAll('.send-request-btn').forEach(button => {
            button.addEventListener('click', () => {
                const studentId = button.getAttribute('data-student-id');
                const tutorId = button.getAttribute('data-tutor-id');

                if (!studentId || !tutorId) {
                    alert('Invalid student or tutor ID.');
                    return;
                }

                // Send AJAX request to the same page
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
                        button.disabled = true; // Disable the button after sending the request
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
=======
    <script>
    document.querySelectorAll('.send-request-btn').forEach(button => {
        button.addEventListener('click', () => {
            const studentId = button.getAttribute('data-student-id');
            const tutorId = button.getAttribute('data-tutor-id');

            if (!studentId) {
                alert('You must be logged in as a student to send a request.');
                window.location.href = 'login.php';
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
>>>>>>> aed8ca917fbbebc5f5147a16ec21f57ea48e7b1b
    </script>
</body>
</html>
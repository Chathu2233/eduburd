<?php
session_start();
require 'db.php'; // Include your DB connection

// Handle the "Send Request" functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['tutor_id'])) {
    $student_id = $_POST['student_id'];
    $tutor_id = $_POST['tutor_id'];
    $status = 'pending'; // Default status
    $date = date('Y-m-d'); // Current date

    try {
        // Insert the request into the tutor_student_request table
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

        // Return success response
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        // Return error response
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Fetch data from the tutor table
try {
    $stmt = $pdo->prepare("
        SELECT 
            t.tutor_id,
            u.first_name, 
            u.last_name, 
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/findatutor.css">
</head>
<body>

    <!-- Header Section -->

    <header>
        <?php
    // Dynamically include the correct header based on user role
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'admin':
                include 'header_admin.php';
                break;
            case 'student':
                echo "Loading student header...";
                include 'header_student.php';
                break;
            case 'tutor':
                include 'header_tutor.php';
                break;
            case 'parent':
                include 'header_parent.php';
                break;
            default:
                include 'header_guest.php'; // Fallback for unknown roles
        }
    } else {
        include 'header_guest.php'; // For guests (not logged in)
    }
?>
    </header>

 <!-- Content Section -->
 <div class="content-wrapper">
    <!-- Page Breadcrumb -->
    <div class="breadcrumb">
        <p>Homepage &gt; Find a tutor </p>
    </div>
    
    <div class="search-bar">
        <input type="text" placeholder="Search for a tutor...">
        <button class="search-btn">🔍</button>
    </div>
    <!-- Main Content -->
    <div class="container">
        <!-- Sidebar Filters -->
        <aside class="sidebar">
            <h2>Filter Tutors</h2>
            <div class="filter">
                <label for="subject">Subject</label>
                <select id="subject">
                    <option>All Subjects</option>
                    <option>Mathematics</option>
                    <option>Science</option>
                    <option>English</option>

                    <!-- Add more subjects as needed -->
                </select>
            </div>
            <div class="filter">
                <label for="level">Education Level</label>
                <select id="level">
                    <option>All Levels</option>
                    <option>Primary</option>
                    <option>Secondary</option>
                    <option>IGCSE</option>
                    <option>AS & A2</option>
                </select>
            </div>
           
        </aside>

        <!-- Tutor List -->
        <main class="tutor-list">
            <?php if (empty($tutors)): ?>
                <p>No tutors found.</p>
            <?php else: ?>
                <?php foreach ($tutors as $tutor): ?>
                    <div class="tutor">
                        <div class="tutor-info">
                            <h3><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h3>
                            <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($tutor['years_of_experience']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($tutor['description']); ?></p>
                            
                            <!-- Star Rating -->
                            <div class="star-rating">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>

                            <!-- Send Request Button -->
                            
                            <button 
                                class="send-request-btn" 
                                data-student-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" 
                                data-tutor-id="<?php echo htmlspecialchars($tutor['tutor_id']); ?>">
                                Send Request
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    </div>
   <!-- Footer Section -->
   <?php include __DIR__ . '/footer.php'; ?>
    <script>

// Function to simulate viewing profile - replace with actual routing as needed


// Add event listeners to filter dropdowns to dynamically filter tutors
document.getElementById('subject').addEventListener('change', filterTutors);
document.getElementById('level').addEventListener('change', filterTutors);
document.getElementById('price').addEventListener('change', filterTutors);

function filterTutors() {
    const selectedSubject = document.getElementById('subject').value;
    const selectedLevel = document.getElementById('level').value;
    const selectedPrice = document.getElementById('price').value;

    const tutors = document.querySelectorAll('.tutor');
    tutors.forEach(tutor => {
        let matchesFilter = true;

        if (selectedSubject !== 'All Subjects' && !tutor.innerHTML.includes(selectedSubject)) {
            matchesFilter = false;
        }
        if (selectedLevel !== 'All Levels' && !tutor.innerHTML.includes(selectedLevel)) {
            matchesFilter = false;
        }
        if (selectedPrice !== 'All' && !tutor.innerHTML.includes(selectedPrice)) {
            matchesFilter = false;
        }

        tutor.style.display = matchesFilter ? 'flex' : 'none';
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

    </script>
</body>
</html>

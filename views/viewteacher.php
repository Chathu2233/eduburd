<?php
require_once 'constants.php';
require 'db.php';

// Get tutor_id from the URL
if (!isset($_GET['tutor_id'])) {
    die("Tutor ID not provided.");
}
$tutor_id = $_GET['tutor_id'];

try {
    // Fetch the number of classes taught by the tutor
    $stmt = $pdo->prepare("
        SELECT COUNT(grade_class_id) AS classes_taught
        FROM grade_class
        WHERE tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $classes_taught = $result['classes_taught'] ?? 0; // Default to 0 if no classes are found
} catch (PDOException $e) {
    die("Error fetching classes taught: " . $e->getMessage());
}

// Fetch subjects taught by the tutor
try {
    $stmt = $pdo->prepare("
        SELECT c.name AS subject_name
        FROM tutor_course tc
        JOIN course c ON tc.course_id = c.course_id
        WHERE tc.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching subjects: " . $e->getMessage());
}

// Fetch the fee for the tutor
try {
    $stmt = $pdo->prepare("
         SELECT t.fee, t.description, u.profile_photo, u.first_name, u.last_name
        FROM tutor t
        JOIN user u ON t.user_id = u.user_id
        WHERE t.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    $fees = $tutor['fee'] ?? 'N/A'; // Default to 'N/A' if no fee is found
    $description = $tutor['description'] ?? 'No description available.'; // Default to a fallback message
    $profile_photo = $tutor['profile_photo'] ?? 'default-profile.png'; // Default to a placeholder image if no image is found
    $tutor_name = $tutor['first_name'] . ' ' . $tutor['last_name']; // Combine first and last name
} catch (PDOException $e) {
    die("Error fetching tutor details: " . $e->getMessage());
}

// Fetch availability from the time_slot table
try {
    $stmt = $pdo->prepare("
        SELECT day, start_time, end_time
        FROM time_slot
        WHERE tutor_id = :tutor_id AND status = 'pending'
        ORDER BY FIELD(day, 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), start_time
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching availability: " . $e->getMessage());
}

// Fetch feedback for the tutor
try {
    $stmt = $pdo->prepare("
        SELECT f.rating, f.comments
        FROM feedback f
        JOIN grade_class gc ON f.grade_class_id = gc.grade_class_id
        WHERE gc.tutor_id = :tutor_id
        ORDER BY f.feedback_id DESC
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching feedback: " . $e->getMessage());
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
    <link rel="stylesheet" href="../assets/css/viewteacher.css">
    <script>
        // AJAX function to send a request
        function sendRequest(tutorId, studentId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_request.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById("request-btn").innerText = "Sent";
                        document.getElementById("request-btn").disabled = true;
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            };
            xhr.send("tutor_id=" + tutorId + "&student_id=" + studentId);
        }
    </script>
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
       

        <!-- Tutor Search Section -->
        <div class="tutor-search-section">
            <div class="tutor-details">
                <div class="tutor-profile">
                <img src="<?= ROOT ?>/<?= htmlspecialchars($tutor['profile_photo']) ?>" alt="Profile Image"style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin: 10px auto; display: block;">                    
                 <div class="tutor-info">
                 <h2><?= htmlspecialchars($tutor_name) ?></h2>
                        <p>Classes Taught: <?= htmlspecialchars($classes_taught) ?></p>
                        <p>Subjects: 
                            <?php if (!empty($subjects)): ?>
                                <?= htmlspecialchars(implode(', ', array_column($subjects, 'subject_name'))) ?>
                            <?php else: ?>
                                No subjects found.
                            <?php endif; ?>
                        </p>
                        <p>Price: Rs <?= htmlspecialchars($fees) ?> per month</p>
                    </div>
                   
                </div>
            </div>
        </div>

        <!-- Content Placeholder (Tutor Information) -->
        <div class="content-placeholder">
            <div class="placeholder-block">
                <div class="content">
                    <h3>About Me</h3>
                    <p><?= nl2br(htmlspecialchars($description)) ?></p>                </div>
            </div>

            


    </div>
                    

        <!-- Reviews Section -->
        <section class="reviews-section">
            <h2>Reviews</h2>
            <div class="rating-summary">
                <div class="overall-rating">
                    <p>4.0</p>
                    <span>★ ★ ★ ★ ☆</span>
                    <p>based on 146, 951 ratings</p>
                </div>
                <div class="rating-breakdown">
                    <div class="rating-bar"><span>★★★★★</span><div class="bar"><div class="fill" style="width: 90%;"></div></div></div>
                    <div class="rating-bar"><span>★★★★</span><div class="bar"><div class="fill" style="width: 5%;"></div></div></div>
                    <div class="rating-bar"><span>★★★</span><div class="bar"><div class="fill" style="width: 2%;"></div></div></div>
                    <div class="rating-bar"><span>★★</span><div class="bar"><div class="fill" style="width: 2%;"></div></div></div>
                    <div class="rating-bar"><span>★</span><div class="bar"><div class="fill" style="width: 1%;"></div></div></div>
                </div>
            </div>

            <!-- Reviews Section -->
        <section class="reviews-section">
            <h2>Reviews</h2>
            <div class="user-comments">
                <?php if (!empty($feedbacks)): ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="comment">
                            <p><strong>Rating:</strong> <?= htmlspecialchars($feedback['rating']) ?> ★</p>
                            <p><?= nl2br(htmlspecialchars($feedback['comments'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews available for this tutor.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

            
        </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit Tutor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveTutor'])) {
        $tutor_id = $_POST['tutor_id'] ?? null;
        $tutorFirstName = $_POST['tutorFirstName'];
        $tutorLastName = $_POST['tutorLastName'];
        $tutorEmail = $_POST['tutorEmail'];
        $tutorExperience = $_POST['tutorExperience'];
        $tutorCV = $_FILES['tutorCV']['name'];

        // Upload CV file
        $targetDir = dirname(__DIR__) . "/uploads/";
        $targetFile = $targetDir . basename($tutorCV);
        move_uploaded_file($_FILES['tutorCV']['tmp_name'], $targetFile);

        if (!empty($tutor_id)) {
            // Update existing tutor
            $query = "UPDATE user u
                      INNER JOIN tutor t ON u.user_id = t.user_id
                      SET u.first_name = :first_name, u.last_name = :last_name, u.email = :email, t.years_of_experience = :years_of_experience, t.cv = :cv
                      WHERE t.tutor_id = :tutor_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':first_name' => $tutorFirstName,
                ':last_name' => $tutorLastName,
                ':email' => $tutorEmail,
                ':years_of_experience' => $tutorExperience,
                ':cv' => $tutorCV,
                ':tutor_id' => $tutor_id
            ]);
        } else {
            // Insert new tutor
            $pdo->beginTransaction();
            try {
                // Insert into user table
                $query = "INSERT INTO user (first_name, last_name, email, user_role) VALUES (:first_name, :last_name, :email, 'tutor')";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':first_name' => $tutorFirstName,
                    ':last_name' => $tutorLastName,
                    ':email' => $tutorEmail
                ]);
                $user_id = $pdo->lastInsertId();

                // Insert into tutor table
                $query = "INSERT INTO tutor (user_id, years_of_experience, cv) VALUES (:user_id, :years_of_experience, :cv)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':years_of_experience' => $tutorExperience,
                    ':cv' => $tutorCV
                ]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        }

        header('Location: ' . ROOT . '/views/admin/managetutors.php');
        exit();
    }

    // Handle Delete Tutor
    if (isset($_POST['deleteTutor'])) {
        $tutor_id = $_POST['tutor_id'];

        // Delete related records in the tutorial table
        $query = "DELETE t FROM tutorial t
                  INNER JOIN grade_class gc ON t.grade_class_id = gc.grade_class_id
                  WHERE gc.tutor_id = :tutor_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':tutor_id' => $tutor_id]);

        // Delete related records in the feedback table
        $query = "DELETE f FROM feedback f
                  INNER JOIN grade_class gc ON f.grade_class_id = gc.grade_class_id
                  WHERE gc.tutor_id = :tutor_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':tutor_id' => $tutor_id]);

        // Delete related records in the grade_class table
        $query = "DELETE FROM grade_class WHERE tutor_id = :tutor_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':tutor_id' => $tutor_id]);

        // Delete the tutor and the associated user
        $query = "DELETE t, u FROM tutor t
                  INNER JOIN user u ON t.user_id = u.user_id
                  WHERE t.tutor_id = :tutor_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':tutor_id' => $tutor_id]);

        // Redirect to the same page after deletion
        header('Location: ' . ROOT . '/views/admin/managetutors.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tutors</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/managetutors.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Manage Tutors</h1>
        <div class="button-container">
            <button onclick="toggleForm()">Add Tutor</button>
        </div>

        <!-- Tutor List -->
        <div class="tutor-list">
            <h2>Tutor List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tutor ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Experience</th>
                        <th>CV</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch tutors from the database
                    $query = "
                        SELECT 
                            u.first_name, 
                            u.last_name, 
                            u.email, 
                            t.tutor_id, 
                            t.years_of_experience, 
                            t.cv 
                        FROM 
                            user u 
                        INNER JOIN 
                            tutor t 
                        ON 
                            u.user_id = t.user_id
                    ";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['tutor_id']}</td>
                            <td>{$row['first_name']}</td>
                            <td>{$row['last_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['years_of_experience']} years</td>
                            <td>
                                <a href=\"" . ROOT . "/views/uploads/{$row['cv']}\" target=\"_blank\" class=\"view-cv-btn\">View CV</a>
                            </td>
                            <td>
                                <button onclick=\"editTutor({$row['tutor_id']}, '{$row['first_name']}', '{$row['last_name']}', '{$row['email']}', '{$row['years_of_experience']}')\">Edit</button>
                                <form action='' method='POST' style='display:inline;' onsubmit=\"return confirmDelete();\">
                                    <input type='hidden' name='tutor_id' value='{$row['tutor_id']}'>
                                    <button type='submit' name='deleteTutor'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Tutor Form -->
        <div id="tutorForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Tutor</h2>
            <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateTutorForm();">
                <input type="hidden" id="tutorId" name="tutor_id">

                <!-- First Name -->
                <div class="form-group">
                    <label for="tutorFirstName">First Name</label>
                    <input type="text" id="tutorFirstName" name="tutorFirstName" placeholder="Enter First Name" required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="tutorLastName">Last Name</label>
                    <input type="text" id="tutorLastName" name="tutorLastName" placeholder="Enter Last Name" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="tutorEmail">Email</label>
                    <input type="email" id="tutorEmail" name="tutorEmail" placeholder="Enter Email" required>
                </div>

                <!-- Years of Experience -->
                <div class="form-group">
                    <label for="tutorExperience">Years of Experience</label>
                    <input type="number" id="tutorExperience" name="tutorExperience" placeholder="Enter Years of Experience" required>
                </div>

                <!-- CV Upload -->
                <div class="form-group">
                    <label for="tutorCV">Upload CV</label>
                    <input type="file" id="tutorCV" name="tutorCV" accept=".pdf,.doc,.docx" required>
                </div>

                <button type="submit" name="saveTutor">Save Tutor</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function toggleForm() {
        // Show the form
        document.getElementById('tutorForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Tutor';
        document.getElementById('tutorId').value = '';
        document.getElementById('tutorFirstName').value = '';
        document.getElementById('tutorLastName').value = '';
        document.getElementById('tutorEmail').value = '';
        document.getElementById('tutorExperience').value = '';

        // Scroll to the form
        document.getElementById('tutorForm').scrollIntoView({ behavior: 'smooth' });
    }

    function editTutor(id, firstName, lastName, email, experience) {
        // Show the form
        document.getElementById('tutorForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Tutor';

        // Pre-fill the form fields
        document.getElementById('tutorId').value = id;
        document.getElementById('tutorFirstName').value = firstName;
        document.getElementById('tutorLastName').value = lastName;
        document.getElementById('tutorEmail').value = email;
        document.getElementById('tutorExperience').value = experience;

        // Scroll to the form
        document.getElementById('tutorForm').scrollIntoView({ behavior: 'smooth' });
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this tutor? All associated data will be permanently deleted.');
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation regex
        return emailRegex.test(email);
    }

    function validateTutorForm() {
        const email = document.getElementById('tutorEmail').value;

        if (!validateEmail(email)) {
            alert('Please enter a valid email address.');
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }
</script>

<form id="confirmDeleteForm" method="POST" style="display: none;">
    <input type="hidden" name="tutor_id" value="<?php echo $tutor_id; ?>">
    <input type="hidden" name="confirmDeleteTutor" value="1">
</form>

</body>
</html>

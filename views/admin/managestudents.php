<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit Student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveStudent'])) {
        $student_id = $_POST['student_id'] ?? null;
        $studentFirstName = $_POST['studentFirstName'];
        $studentLastName = $_POST['studentLastName'];
        $studentEmail = $_POST['studentEmail'];
        $studentDOB = $_POST['studentDOB'];
        $studentContact = $_POST['studentContact'];

        if (!empty($student_id)) {
            // Update existing student
            $query = "UPDATE user u
                      INNER JOIN student s ON u.user_id = s.user_id
                      SET u.first_name = :first_name, u.last_name = :last_name, u.email = :email, u.dob = :dob, u.contact_no = :contact_no
                      WHERE s.student_id = :student_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':first_name' => $studentFirstName,
                ':last_name' => $studentLastName,
                ':email' => $studentEmail,
                ':dob' => $studentDOB,
                ':contact_no' => $studentContact,
                ':student_id' => $student_id
            ]);
        } else {
            // Insert new student
            $pdo->beginTransaction();
            try {
                // Insert into user table
                $query = "INSERT INTO user (first_name, last_name, email, dob, contact_no, user_role) VALUES (:first_name, :last_name, :email, :dob, :contact_no, 'student')";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':first_name' => $studentFirstName,
                    ':last_name' => $studentLastName,
                    ':email' => $studentEmail,
                    ':dob' => $studentDOB,
                    ':contact_no' => $studentContact
                ]);
                $user_id = $pdo->lastInsertId();

                // Insert into student table
                $query = "INSERT INTO student (user_id) VALUES (:user_id)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':user_id' => $user_id
                ]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        }

        header('Location: ' . ROOT . '/views/admin/managestudents.php');
        exit();
    }

    // Handle Delete Student
    if (isset($_POST['deleteStudent'])) {
        $student_id = $_POST['student_id'];
        $query = "DELETE s, u FROM student s
                  INNER JOIN user u ON s.user_id = u.user_id
                  WHERE s.student_id = :student_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':student_id' => $student_id]);

        header('Location: ' . ROOT . '/views/admin/managestudents.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/managestudents.css">
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
        <h1>Manage Students</h1>
        <div class="button-container">
            <button class="add-student-btn" onclick="toggleForm()">Add Student</button>
        </div>

        <!-- Student List -->
        <div class="student-list">
            <h2>Student List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch students from the database
                    $query = "
                        SELECT 
                            u.first_name, 
                            u.last_name, 
                            u.email, 
                            u.dob, 
                            u.contact_no, 
                            s.student_id 
                        FROM 
                            user u 
                        INNER JOIN 
                            student s 
                        ON 
                            u.user_id = s.user_id
                    ";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['student_id']}</td>
                            <td>{$row['first_name']}</td>
                            <td>{$row['last_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['dob']}</td>
                            <td>{$row['contact_no']}</td>
                            <td>
                                <button onclick=\"editStudent({$row['student_id']}, '{$row['first_name']}', '{$row['last_name']}', '{$row['email']}', '{$row['dob']}', '{$row['contact_no']}')\">Edit</button>
                                <form action='' method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this student?');\">
                                    <input type='hidden' name='student_id' value='{$row['student_id']}'>
                                    <button type='submit' name='deleteStudent'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Student Form -->
        <div id="studentForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Student</h2>
            <form action="" method="POST" onsubmit="return validateStudentForm();">
                <input type="hidden" id="studentId" name="student_id">

                <!-- First Name -->
                <div class="form-group">
                    <label for="studentFirstName">First Name</label>
                    <input type="text" id="studentFirstName" name="studentFirstName" placeholder="Enter First Name" required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="studentLastName">Last Name</label>
                    <input type="text" id="studentLastName" name="studentLastName" placeholder="Enter Last Name" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="studentEmail">Email</label>
                    <input type="email" id="studentEmail" name="studentEmail" placeholder="Enter Email" required>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label for="studentDOB">Date of Birth</label>
                    <input type="date" id="studentDOB" name="studentDOB" required>
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label for="studentContact">Contact</label>
                    <input type="text" id="studentContact" name="studentContact" placeholder="Enter Contact Number" required>
                </div>

                <button type="submit" name="saveStudent">Save Student</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation regex
        return emailRegex.test(email);
    }

    function validateDOB(dob) {
        const dobDate = new Date(dob);
        const today = new Date();
        const age = today.getFullYear() - dobDate.getFullYear();
        const monthDiff = today.getMonth() - dobDate.getMonth();

        // Ensure the user is at least 5 years old
        return age > 5 || (age === 5 && monthDiff >= 0);
    }

    function validateContact(contact) {
        const contactRegex = /^[0-9]{10}$/; // Validate 10-digit phone numbers
        return contactRegex.test(contact);
    }

    function validateStudentForm() {
        const email = document.getElementById('studentEmail').value;
        const dob = document.getElementById('studentDOB').value;
        const contact = document.getElementById('studentContact').value;

        if (!validateEmail(email)) {
            alert('Please enter a valid email address.');
            return false; // Prevent form submission
        }

        if (!validateDOB(dob)) {
            alert('Please enter a valid Date of Birth. The student must be at least 5 years old.');
            return false; // Prevent form submission
        }

        if (!validateContact(contact)) {
            alert('Please enter a valid 10-digit contact number.');
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }

    function toggleForm() {
        document.getElementById('studentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Student';
        document.getElementById('studentId').value = '';
        document.getElementById('studentFirstName').value = '';
        document.getElementById('studentLastName').value = '';
        document.getElementById('studentEmail').value = '';
        document.getElementById('studentDOB').value = '';
        document.getElementById('studentContact').value = '';

        // Scroll to the form
        document.getElementById('studentForm').scrollIntoView({ behavior: 'smooth' });
    }

    function editStudent(id, firstName, lastName, email, dob, contact) {
        document.getElementById('studentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Student';

        // Pre-fill the form fields
        document.getElementById('studentId').value = id;
        document.getElementById('studentFirstName').value = firstName;
        document.getElementById('studentLastName').value = lastName;
        document.getElementById('studentEmail').value = email;
        document.getElementById('studentDOB').value = dob;
        document.getElementById('studentContact').value = contact;

        // Scroll to the form
        document.getElementById('studentForm').scrollIntoView({ behavior: 'smooth' });
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this student?');
    }
</script>
</body>
</html>

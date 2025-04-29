<?php

session_start();

include('../connect.php'); // Include database connection

// Simulate logged-in user
$user_id = 22; // Replace this with the logged-in user's ID from the session or authentication system
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

// Fetch existing data for the form
$query = "
    SELECT 
        u.first_name, u.last_name, u.contact_no, u.email, 
        p.nic 
    FROM user u 
    LEFT JOIN parent p ON u.user_id = p.parent_id 
    WHERE u.user_id = $user_id";


$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Fetch user data for display in the form
} else {
    die("No user found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstName'];
    $lastname = $_POST['lastName'];
    $contactnumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $nic = $_POST['nic'];

    // Update the `user` table
    $update_user_query = "
        UPDATE user 
        SET first_name = '$firstname', 
            last_name = '$lastname', 
            contact_no = '$contactnumber', 
            email = '$email' 
        WHERE user_id = $user_id";

    // Update the `parent` table
    $update_parent_query = "
        UPDATE parent 
        SET nic = '$nic' 
        WHERE parent_id = $user_id";

       
if (isset($_GET['teaching_mode']) && in_array($_GET['teaching_mode'], ['online', 'physical'])) {
    $filters[] = "t.teaching_mode = :teaching_mode";
    $params[':teaching_mode'] = $_GET['teaching_mode'];

    if (!empty($filters)) {
        $sql .= " WHERE " . implode(" AND ", $filters);
    }
    if (isset($_GET['teaching_mode']) && $_GET['teaching_mode'] === 'online') {
        $filters[] = "u.city = 'Colombo'";
    }
    

    if ($conn->query($update_user_query) && $conn->query($update_parent_query)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='parentprofile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}
?>
$notifications = []; // create an empty notifications array

$stmt = $pdo->prepare("
    SELECT parent_student_request_id 
    FROM parent_student_request 
    WHERE status = 'accepted' 
      AND is_read = 0
    ORDER BY date DESC
");
$stmt->execute();

$acceptedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($acceptedRequests as $request) {
    $notifications[] = "New accepted request: Request ID {$request['parent_student_request_id']}";
}



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/editprofile.css">
    <link rel="stylesheet" href="css/header .css">
   
</head>
<body>
 <!-- Header Section -->
 <header>
        <?php include '../header_parent.php'; ?>
    </header>
 <header>
    <div class="logo">
        <img src="images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo">
    </div>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Subjects</a></li>
            <li><a href="#">Find A Tutor</a></li>
            <li><a href="#">Become A Tutor</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </nav>
    <div class="auth-buttons">
        <a href="#" class="logout-btn">Login</a>
    </div>
</header>
    <div align="center" >
        <h1>Edit Profile</h1>
        <h2>Personal Information</h2>

        <form action="#" method="post">
            <div class="input-row">
                <div class="input-group">
                    <label for="firstName">First name</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="input-group">
                    <label for="lastName">Last name</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label for="nic">NIC</label>
                    <input type="text" id="nic" name="nic" required>
             
                </div>
                <div class="input-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" required>
                </div>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
    <label for="teaching_mode">Teaching Mode</label>
    <select id="teaching_mode" name="teaching_mode">
        <option value="">-- Select Mode --</option>
        <option value="online" <?php echo (isset($_GET['teaching_mode']) && $_GET['teaching_mode'] === 'online') ? 'selected' : ''; ?>>Online</option>
        <option value="physical" <?php echo (isset($_GET['teaching_mode']) && $_GET['teaching_mode'] === 'physical') ? 'selected' : ''; ?>>Physical</option>
    </select>
</div>

}



            <button type="submit" class="save-button">Save</button>
        </form>

        <div class="saved-message">
            
        </div>
    </div>

    <?php include '../footer.php'; ?>

    <script>
   document.getElementById('signupForm').addEventListener('submit', function (e) {
    let hasError = false;

    const nicInput = document.getElementById('nic').value.trim();
    const dobInput = document.getElementById('dob').value.trim();
    const nicError = document.getElementById('nic-error');

    const oldNicPattern = /^\d{9}[vVxX]$/; 
    const newNicPattern = /^\d{12}$/;       

    const dobYear = new Date(dobInput).getFullYear();

    if (newNicPattern.test(nicInput)) {
        const nicYear = parseInt(nicInput.substring(0, 4), 10);

        if (nicYear !== dobYear) {
            nicError.textContent = "NIC birth year and DOB year must match.";
            nicError.style.display = 'block';
            hasError = true;
        } else {
            nicError.style.display = 'none';
        }
    } else if (oldNicPattern.test(nicInput)) {
        const nicYear = parseInt(nicInput.substring(0, 2), 10) + 1900; 
        const nicYear = parseInt(nicInput.substring(0, 4), 10);

        if (nicYear !== dobYear) {
            nicError.textContent = "NIC birth year and DOB year must match.";
            nicError.style.display = 'block';
            hasError = true;
        } else {
            nicError.style.display = 'none';
        }
    } else {
        nicError.textContent = "Invalid NIC format. Must be 12 digits or 9 digits followed by V or X.";
        nicError.style.display = 'block';
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});
</script>


    

</body>
</html>

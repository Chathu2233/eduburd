<?php
session_start();
require_once '../constants.php';
require '../db.php';

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "
    SELECT 
        user.first_name AS firstname, 
        user.last_name AS lastname, 
        user.email, 
        user.contact_no AS contactnumber, 
        parent.nic
    FROM parent
    JOIN user ON parent.user_id = user.user_id
    WHERE parent.user_id = :user_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "No profile found for the logged-in user.";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $countryCode = $_POST['countryCode'];
    $contactnumber = $_POST['contactnumber'];
    $email = $_POST['email'];
    $nic = $_POST['nic'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    // Combine country code and contact number
    $fullContactNumber = $countryCode . " " . $contactnumber;

    // Validate contact number
    if (!preg_match('/^\+(\d{1,3})\s?\d{9,10}$/', $fullContactNumber)) {
        echo "Invalid contact number format.";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        echo "Email must be a valid Gmail address.";
        exit;
    }

    // Validate passwords
    if ($password !== $repassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Update user details in the database
    $query = "
        UPDATE user
        JOIN parent ON user.user_id = parent.user_id
        SET user.first_name = :firstname,
            user.last_name = :lastname,
            user.email = :email,
            user.contact_no = :contactnumber,
            parent.nic = :nic
    ";
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", user.password = :password";
    }
    $query .= " WHERE parent.user_id = :user_id";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':contactnumber', $fullContactNumber, PDO::PARAM_STR);
    $stmt->bindParam(':nic', $nic, PDO::PARAM_STR);
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    }
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to the profile page
    header('Location: parentprofilepage.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/parentprofile.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar1_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="profile-container">
                <h2>Edit Profile</h2>
                
                <form action="" method="post" class="profile-details" onsubmit="return validateForm()">
                    <div class="profile-box">
                        <label for="firstname"><strong>First Name:</strong></label>
                        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                    </div>

                    <div class="profile-box">
                        <label for="lastname"><strong>Last Name:</strong></label>
                        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                    </div>

                    <div class="profile-box">
    <label for="contactnumber"><strong>Contact Number:</strong></label>
    <div class="contact-details" style="display: flex; align-items: left; gap: 10px;">
        <!-- Dropdown for country codes -->
        <select id="country-code" name="countryCode" class="form-control" required>
            <option value="+94" <?php echo (strpos($row['contactnumber'], '+94') === 0) ? 'selected' : ''; ?>>+94 (Sri Lanka)</option>
            <option value="+91" <?php echo (strpos($row['contactnumber'], '+91') === 0) ? 'selected' : ''; ?>>+91 (India)</option>
            <option value="+44" <?php echo (strpos($row['contactnumber'], '+44') === 0) ? 'selected' : ''; ?>>+44 (UK)</option>
            <option value="+1" <?php echo (strpos($row['contactnumber'], '+1') === 0) ? 'selected' : ''; ?>>+1 (USA)</option>
            <option value="+61" <?php echo (strpos($row['contactnumber'], '+61') === 0) ? 'selected' : ''; ?>>+61 (Australia)</option>
        </select>
        <!-- Input for contact number -->
        <input type="text" id="contactnumber" name="contactnumber" class="form-control" value="<?php echo htmlspecialchars(preg_replace('/^\+\d+\s?/', '', $row['contactnumber'])); ?>" placeholder="712345678" required>
    </div>
    <span id="contact-error" style="color: red; font-size: 14px; display: none;">Invalid contact number format</span>
</div>

                    <div class="profile-box">
                        <label for="email"><strong>E-mail:</strong></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" pattern="[a-z0-9._%+-]+@gmail\.com$" required>
                    </div>

                    <div class="profile-box">
                        <label for="nic"><strong>NIC:</strong></label>
                        <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($row['nic']); ?>" required>
                    </div>

                    <div class="profile-box">
                        <label for="password"><strong>New Password:</strong></label>
                        <input type="password" id="password" name="password" placeholder="Enter new password">
                    </div>

                    <div class="profile-box">
                        <label for="repassword"><strong>Re-enter Password:</strong></label>
                        <input type="password" id="repassword" name="repassword" placeholder="Confirm new password">
                    </div>

                    <div class="button-container">
                        <button type="submit" class="edit-button">Save</button>
                        <button type="button" class="edit-button cancel-button" onclick="window.location.href='parentprofilepage.php'">Cancel</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>

    <script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var repassword = document.getElementById("repassword").value;
        var countryCode = document.getElementById("country-code").value;
        var contactNumber = document.getElementById("contactnumber").value;
        var contactError = document.getElementById("contact-error");

        // Combine country code and contact number
        var fullContactNumber = countryCode + " " + contactNumber;

        // Regex pattern to validate full contact number
        var pattern = /^\+(\d{1,3})\s?\d{9,10}$/; // Example: +94 712345678

        // Validate the full contact number
        if (!pattern.test(fullContactNumber)) {
            contactError.style.display = 'block';
            return false;
        } else {
            contactError.style.display = 'none';
        }

        // Validate passwords
        if (password !== repassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
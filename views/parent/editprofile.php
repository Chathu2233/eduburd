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
    $contactnumber = $_POST['contactnumber'];
    $email = $_POST['email'];
    $nic = $_POST['nic'];

    // Update user details in the database
    $query = "
        UPDATE user
        JOIN parent ON user.user_id = parent.user_id
        SET user.first_name = :firstname,
            user.last_name = :lastname,
            user.email = :email,
            user.contact_no = :contactnumber,
            parent.nic = :nic
        WHERE parent.user_id = :user_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':contactnumber', $contactnumber, PDO::PARAM_STR);
    $stmt->bindParam(':nic', $nic, PDO::PARAM_STR);
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
                
                <form action="" method="post" class="profile-details">
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
                        <input type="text" id="contactnumber" name="contactnumber" value="<?php echo htmlspecialchars($row['contactnumber']); ?>" required>
                    </div>

                    <div class="profile-box">
                        <label for="email"><strong>E-mail:</strong></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                    </div>

                    <div class="profile-box">
                        <label for="nic"><strong>NIC:</strong></label>
                        <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($row['nic']); ?>" required>
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
</body>
</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/my_profile.css">
</head>
<body>
    
<header>
    <?php
    include '../header_tutor.php'
    ?>
    </header>
    <div class="profile-container">
        <div class="profile-section">
            <h2>My Profile</h2>
            
            <div class="profile-image">
                <img src="../../assets/images/acc.jpeg" alt="Profile Image">
            </div>
            <div class="profile-details">
                <p><strong>First Name:</strong>Ayathma </p>
                <p><strong>Last Name:</strong>Amanethmi </p>
                <p><strong>E-Mail:</strong>ayathma@gmail.com </p>
                <p><strong>Contact Number:</strong>0756613241</p>
                <p><strong>Description:</strong>Hehe</p>
            </div>
            <a href="editprofile.php" class="edit-button">Edit Profile</a>


        </div>
    </div>
     <!-- Footer Section -->
     <?php include '../footer.php'; ?>
</body>
</html>

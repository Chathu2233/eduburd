
<?php
session_start();
require_once '../constants.php';
require '../db.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Profile</title>
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
        <?php include __DIR__ . '/sidebar2_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="profile-container">
                <h2>Child Profile</h2>
                <img src="<?php echo ROOT; ?>/assets/images/dashboard.png" alt="Profile Picture">
                <div class="profile-details">
                    <div class="profile-box">
                        <p><strong>Student ID: </strong> </p>
                    </div>
                    <div class="profile-box">
                        <p><strong>First Name: </strong> </p>
                    </div>
                    <div class="profile-box">
                        <p><strong>Last Name: </strong> </p>
                    </div>
                    
                    <div class="profile-box">
                        <p><strong> Grade: </strong> </p>
                    </div>
                </div>
                
            </div>
        </main>
    </div>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
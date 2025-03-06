<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/Tutor/editProfile.css">
</head>
<body>

<header>
<?php
    include '../header_student.php'
    ?>
    </header>
    <div class="content">

    <div class="form-container">
        <h1>Edit Profile</h1>
        <h2>Personal Information</h2>

        <form action="#" method="post">
            <div class="input-row">
                <div class="input-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                </div>
                <div class="input-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com" required>
                </div>
                <div class="input-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" placeholder="+1 234 567 890" required>
                </div>
            </div>

            <div class="input-group">


            <div class="input-row">
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="input-group">
                    <label for="rePassword">Re-enter Password</label>
                    <input type="password" id="rePassword" name="rePassword" placeholder="Confirm new password" required>
                </div>
            </div>

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </div>
    </div>
<!-- Footer Section -->
<?php include '../footer.php'; ?>  
</body>
    
</html>

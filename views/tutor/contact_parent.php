<?php
session_start();
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
    <link rel="stylesheet" href="../../assets/css/Tutor/contact_parent.css">
    <link rel="stylesheet" href="../../assets/css/Tutor/navbar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>

    <!-- Header Section -->

    <header>
    <?php
    include '../header_tutor.php'
    ?>
</header>


    <div class="container">
        <!-- Header Section -->
        <div class="section-title">
            <h1>Parent Contact Details</h1>
        </div>

        <!-- Parent Details Card -->
        <section class="parent-details-card">
            <div class="parent-photo">
                <img src="../../assets/images/parent.jpg" alt="Parent Photo">
            </div>
            <div class="parent-info">
                <h2>Ayathma Amanethmi</h2>
                <p><strong>Relationship:</strong> Mother</p>
                <p><strong>Child:</strong> Ayathma Amanethmi</p>
                <p><strong>Occupation:</strong> Teacher</p>
            </div>
        </section>

        <!-- Contact Info Section -->
        <section class="contact-info">
            <div class="contact-item">
                <h3>Email:</h3>
                <p>ama@email.com</p>
            </div>
            <div class="contact-item">
                <h3>Phone:</h3>
                <p>70 50 43255</p>
            </div>
            <div class="contact-item">
                <h3>Address:</h3>
                <p>123 Main Street</p>
            </div>
        </section>
    </div>

    <!-- Footer Section -->
    <?php include '../footer.php'; ?>
</body>
</html>

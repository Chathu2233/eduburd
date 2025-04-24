<?php
session_start();
require '../db.php'; // Include database connection

try {
    // Fetch parent details for accepted parent-student requests
    $stmt = $pdo->prepare("
        SELECT 
            p.parent_id,
            u.first_name,
            u.last_name,
            u.email,
            u.contact_no
        FROM parent_student_request psr
        JOIN parent p ON psr.parent_id = p.parent_id
        JOIN user u ON p.user_id = u.user_id
        WHERE psr.status = 'accepted'
    ");
    $stmt->execute();
    $parent_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching parent details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Parent Contact Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/Tutor/contact_parent.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <?php include '../header_student.php'; ?>
    </header>

    <div class="container">
        <!-- Header Section -->
        <div class="section-title">
            <h1>Parent Contact Details</h1>
        </div>

        <!-- Parent Details -->
        <?php if (empty($parent_details)): ?>
            <p>No parent details found.</p>
        <?php else: ?>
            <?php foreach ($parent_details as $parent): ?>
                <!-- Parent Details Card -->
                <section class="parent-details-card">
                    <div class="parent-photo">
                        <img src="../../assets/images/parent.jpg" alt="Parent Photo">
                    </div>
                    <div class="parent-info">
                        <h2><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></h2>

           </div>
                </section>

                <!-- Contact Info Section -->
                <section class="contact-info">
                    <div class="contact-item">
                        <h3>Email:</h3>
                        <p><?php echo htmlspecialchars($parent['email']); ?></p>
                    </div>
                    <div class="contact-item">
                        <h3>Phone:</h3>
                        <p><?php echo htmlspecialchars($parent['contact_no']); ?></p>
                    </div>
                    <div class="contact-item">
                        <h3>Address:</h3>

                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer Section -->
    <?php include '../footer.php'; ?>
</body>
</html>

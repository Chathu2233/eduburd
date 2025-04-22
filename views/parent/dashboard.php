<?php
session_start();
require_once '../constants.php';
require '../db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Fetch announcements for the "parent" audience
try {
    $stmt = $pdo->prepare("
        SELECT text, date 
        FROM admin_announcement 
        WHERE audience = 'parent'
        ORDER BY date DESC
    ");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - EduBurd</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../parent/sidebar1_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Announcements -->
            <section class="announcement-section">
                <h2>Site announcements</h2>
                <?php if (empty($announcements)): ?>
                    <p>No announcements available.</p>
                <?php else: ?>
                    <?php foreach ($announcements as $announcement): ?>
    <div class="announcement">
        <small>Posted on: <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($announcement['date']))); ?></small>
        <p><?php echo htmlspecialchars($announcement['text']); ?></p>
    </div>
<?php endforeach; ?>
                <?php endif; ?>
            </section>

            <!-- FAQs -->
<section class="faq-section">
    <h2>Frequently asked questions (FAQs)</h2>
    <div class="faq-container">
        <div class="faq">
            <button class="faq-question">How do I add my child?</button>
            <div class="faq-answer">
                <p>Click the "Add Child" button and enter your child’s details. Once submitted, your child will be added to the system.</p>
            </div>
        </div>
        <div class="faq">
            <button class="faq-question">How do I check my child’s progress?</button>
            <div class="faq-answer">
                <p>Navigate to the "My Child List" section, select your child, and view their performance details.</p>
            </div>
        </div>
        <div class="faq">
            <button class="faq-question">How do I access parenting tips?</button>
            <div class="faq-answer">
                <p>Visit the "Resource Library" to explore guides, articles, and tips curated for parents.</p>
            </div>
        </div>
        <div class="faq">
            <button class="faq-question">What payment methods are available?</button>
            <div class="faq-answer">
                <p>We accept card payments for your convenience.</p>
            </div>
        </div>
    </div>
</section>
        </main>
    </div>
    <script>
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
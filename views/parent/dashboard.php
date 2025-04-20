<?php
session_start();
require_once '../constants.php';
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
                <h2>Site Announcements</h2>
                <div class="announcement">
                    <p>New Parent Resources are now available! Visit our Resources section to find helpful guides on supporting your child's learning at home.</p>
                </div>
                <div class="announcement">
                    <p>Need tech support? Our IT team is offering virtual help sessions every Wednesday. Sign up online!</p>
                </div>
                <div class="announcement">
                    <p>Please be advised: There will be a vacation for all classes this week due to the Easter Festival.</p>
                </div>
            </section>

            <!-- FAQs -->
            <section class="faq-section">
                <h2>FAQs</h2>
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
                        <p>We accept card payments, Western Union, and PayPal for your convenience.</p>
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
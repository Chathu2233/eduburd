<?php require_once 'constants.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Online Tutoring Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/footer.css">
</head>

<body>
    <footer>
        <div class="footer-container">
            <!-- Logo and Description -->
            <div class="footer-logo">
                <img src="<?php echo ROOT; ?>/assets/images/Modern Marketing Cover Page Document .png" alt="EduBurd Logo"> 
                <p>Empowering learners with top-quality tutoring across a variety of subjects and levels. Join us to enhance your learning journey.</p>
            </div>

            <!-- Footer Links -->
            <div class="footer-links">
                <div class="footer-section">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="<?php echo ROOT; ?>/contact">Contact Us</a></li>
                        <li><a href="<?php echo ROOT; ?>/aboutus">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Programs</h4>
                    <ul>
                        <li><a href="<?php echo ROOT; ?>/programs/primary">Primary</a></li>
                        <li><a href="<?php echo ROOT; ?>/programs/secondary">Secondary</a></li>
                        <li><a href="<?php echo ROOT; ?>/programs/igcse">IGCSE</a></li>
                        <li><a href="<?php echo ROOT; ?>/programs/as&a2">AS & A2</a></li>
                    </ul>
                </div>
                <div class="footer-section" id="contact">
                    <h4>Contact</h4>
                    <ul>
                        <li>Address: UCSC Building Complex, 35 Reid Ave, Colombo 00700 </li>
                        <li>Email: support@eduburd.com</li>
                        <li>Phone: +94 761 166 329</li>
                    </ul>
                </div>

                <!-- Social Media Links -->
                <div class="footer-social">
                    <a href="#"><img src="<?php echo ROOT; ?>/assets/images/facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="<?php echo ROOT; ?>/assets/images/twitter.png" alt="Twitter"></a>
                    <a href="#"><img src="<?php echo ROOT; ?>/assets/images/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="<?php echo ROOT; ?>/assets/images/linkedin.png" alt="LinkedIn"></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2024 EduBurd | All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>
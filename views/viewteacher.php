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
    <link rel="stylesheet" href="../assets/css/viewteacher.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <?php
        // Dynamically include the correct header based on user role
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'admin':
                include 'header_admin.php';
                break;
            case 'student':
                echo "Loading student header...";
                include 'header_student.php';
                break;
            case 'tutor':
                include 'header_tutor.php';
                break;
            case 'parent':
                include 'header_parent.php';
                break;
            default:
                include 'header_guest.php'; // Fallback for unknown roles
        }
    } else {
        include 'header_guest.php'; // For guests (not logged in)
    }
?>
    </header>

 <!-- Content Section -->
 <div class="content-wrapper">
    <!-- Page Breadcrumb -->
    <div class="breadcrumb">
        <p>Homepage &gt; Find a tutor &gt; Teacher name</p>
    </div>
    
    <div class="search-bar">
        <input type="text" placeholder="Search for a tutor...">
        <button class="search-btn">🔍</button>
    </div>

    <!-- Tutor Search Section -->
    <div class="tutor-search-section">
        <div class="tutor-details">
            <div class="tutor-profile">
                <div class="tutor-image"></div>
                <div class="tutor-info">
                    <h2>Tharindu Senanayake</h2>
                    <p>Classes Taught: 200</p>
                    <p>Subjects: Physics</p>
                    <p>Price: LKR 3500 per hour</p>
                </div>
                <button class="request-btn">Send Request</button>
            </div>
        </div>
    </div>

    <!-- Content Placeholder (Tutor Information) -->
    <div class="content-placeholder">
    <div class="placeholder-block">
    <div class="content">
        <h3>About Me</h3>
        <p>Hello! I'm Tharindu Senanayake, and I specialize in Physics tutoring. With over 200 classes taught, I have developed a strong understanding of how to help students grasp the key concepts of Physics and apply them effectively in both exams and practical scenarios. I offer tutoring at an affordable rate of LKR 3500 per hour, ensuring quality education that caters to students at different levels.</p>
       
    </div>
</div>

        <div class="placeholder-block">
        <div class="content">
        <h3>About my sessions</h3>
        <p>In my classes, I focus on making Physics as relatable and engaging as possible. I believe in building a solid foundation by breaking down complex concepts into simple, digestible steps. We start with the basics and slowly build up to more advanced topics. My goal is to help students not only understand the theory but also apply their knowledge to solve real-world problems.</p>
        <p>Through interactive lessons and regular practice, I ensure students become confident in tackling any Physics-related challenge. Whether it's for school exams or simply deepening their understanding, I am here to guide them every step of the way. I'm dedicated to providing continuous support and am always available to answer questions and help with revisions.</p>
        <p>I look forward to working with you to achieve your academic goals!</p>
    </div>
        </div>
        <div class="placeholder-block">
            <div class="content">
            <h3>Qualifications</h3>
        <table class="qualifications">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Qualification</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Maths</td>
                    <td>A-level</td>
                    <td>A</td>
                </tr>
                <tr>
                    <td>History</td>
                    <td>A-level</td>
                    <td>A</td>
                </tr>
                <tr>
                    <td>Economics</td>
                    <td>A-level</td>
                    <td>A</td>
                </tr>
            </tbody>
        </table>

        <h3>General Availability</h3>
        <table class="availability">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Pre 12pm</th>
                    <th>12 - 5pm</th>
                    <th>After 5pm</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mon</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Tue</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Wed</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Thu</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Fri</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Sat</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Sun</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <h3>Subjects Offered</h3>
        <table class="subjects">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Qualification</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Economics</td>
                    <td>GCSE</td>
                    <td>£25 /hr</td>
                </tr>
                <tr>
                    <td>Maths</td>
                    <td>GCSE</td>
                    <td>£25 /hr</td>
                </tr>
                <tr>
                    <td>Maths</td>
                    <td>11 Plus</td>
                    <td>£25 /hr</td>
                </tr>
                <tr>
                    <td>Personal Statements</td>
                    <td>Mentoring</td>
                    <td>£25 /hr</td>
                </tr>
                <tr>
                    <td>Maths</td>
                    <td>13 Plus</td>
                    <td>£25 /hr</td>
                </tr>
                <tr>
                    <td>Maths</td>
                    <td>KS3</td>
                    <td>£25 /hr</td>
                </tr>
            </tbody>
        </table>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <h2>Reviews</h2>
        <div class="rating-summary">
            <div class="overall-rating">
                <p>4.0</p>
                <span>★ ★ ★ ★ ☆</span>
                <p>based on 146, 951 ratings</p>
            </div>
            <div class="rating-breakdown">
                <div class="rating-bar"><span>★★★★★</span><div class="bar"><div class="fill" style="width: 90%;"></div></div></div>
                <div class="rating-bar"><span>★★★★</span><div class="bar"><div class="fill" style="width: 5%;"></div></div></div>
                <div class="rating-bar"><span>★★★</span><div class="bar"><div class="fill" style="width: 2%;"></div></div></div>
                <div class="rating-bar"><span>★★</span><div class="bar"><div class="fill" style="width: 2%;"></div></div></div>
                <div class="rating-bar"><span>★</span><div class="bar"><div class="fill" style="width: 1%;"></div></div></div>
            </div>
        </div>

        <!-- User Comments -->
        <div class="user-comments">
            <div class="comment">
                <p><strong>Laura Hipster</strong></p>
                <p>October 03, 2022</p>
                <p>Great experience with this tutor, highly recommend!</p>
                <a href="#" class="reply-link">Reply</a>
            </div>
            <!-- Additional Comments Here -->
            <div class="comment">
                <p><strong>Laura Hipster</strong></p>
                <p>October 03, 2022</p>
                <p>Great experience with this tutor, highly recommend!</p>
                <a href="#" class="reply-link">Reply</a>
            </div>
            <div class="comment">
                <p><strong>Laura Hipster</strong></p>
                <p>October 03, 2022</p>
                <p>Great experience with this tutor, highly recommend!</p>
                <a href="#" class="reply-link">Reply</a>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <a href="#">&laquo;</a>
            <a href="#" class="active">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">&raquo;</a>
        </div>
    </section>

    <!-- Comment Form -->
    <section class="comment-form-section">
        <h2>Leave A Comment</h2>
        <form>
            <label for="name">Name*</label>
            <input type="text" id="name" required>
            <label for="email">Email*</label>
            <input type="email" id="email" required>
            <label for="comment">Comment*</label>
            <textarea id="comment" required></textarea>
            <label>
                <input type="checkbox"> Save my name, email in this browser for the next time I comment
            </label>
            <button type="submit" class="post-comment-btn">Post Comment</button>
        </form>
    </section>
 </div>
 <?php include 'footer.php'; ?>
</body>
</html>

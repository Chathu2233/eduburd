<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Details</title>
   
    <link rel="stylesheet" href="../../assets/css/parent/seetutor.css">
</head>


<body>

    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>
   

    <div class="container">
        <!-- Header Section -->
        <div class="tutor-info">
            <!-- Profile Picture -->
            <div class="profile-pic">
</div>
            <!-- Tutor Name -->
            <div class="tutor-name">Tharindu Senanayaka</div>

            <!-- View Profile Button -->
            <button class="view-profile" onclick="toggleProfile()">View tutor profile</button>
        </div>

        <!-- Profile Section -->
        <div class="profile-section" id="profile-section">
            <h2>Tutor Profile</h2>
            <div class="profile-details"><strong>First Name:</strong> Amali</div>
            <div class="profile-details"><strong>Last Name:</strong> Weerasinghe</div>
            <div class="profile-details"><strong>Contact Number:</strong> (123) 456-7890</div>
            <div class="profile-details"><strong>Email:</strong> amali@gmail.com</div>
            <div class="profile-details"><strong>Years of Experience:</strong> 5 Years</div>
            
            <div class="profile-details">
                <strong>View CV:</strong> 
                <a href="path/to/tutor_cv.pdf" id="cv-link" target="_blank">Click here to view</a>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="tabs">
            <div class="tab active" onclick="showTab(0)">Class History</div>
            <div class="tab" onclick="showTab(1)">Upcoming Classes</div>
            <div class="tab" onclick="showTab(2)">Payment History</div>
            <div class="tab" onclick="showTab(3)">Pending Assignment</div>
        </div>

        <!-- Tab Contents here -->
        <!-- Class History Tab Content -->
        <div class="tab-content active" id="class-history">
            <h2>Class History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time Joined</th>
                        <th>Time Left</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12/08/2024</td>
                        <td>10:00 AM</td>
                        <td>11:00 AM</td>
                        <td>1 hour</td>
                    </tr>
                    <tr>
                        <td>14/08/2024</td>
                        <td>02:00 PM</td>
                        <td>03:00 PM</td>
                        <td>1 hour</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Upcoming Classes Tab Content -->
        <div class="tab-content" id="upcoming-classes">
            <h2>Upcoming Classes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Topic</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20/11/2024</td>
                        <td>10:00 AM</td>
                        <td>Nouns</td>
                    </tr>
                    <tr>
                        <td>22/11/2024</td>
                        <td>02:00 PM</td>
                        <td>Verbs</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment History Tab Content -->
        <div class="tab-content" id="payment-history">
            <h2>Payment History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10/11/2024</td>
                        <td>$200</td>
                        <td>visa card</td>
                    </tr>
                  
                </tbody>
            </table>
        </div>

        <!-- Pending Homework Tab Content -->
        <div class="tab-content" id="pending-homework">
            <h2>Pending Assignment</h2>
            <table class="homework-table">
                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Assignment 1</td>
                        <td class="status-pending">Pending</td>
                        <td></td>

                    </tr>
                    <tr>
                        <td>Assignment 2</td>
                        <td class="status-done">Done</td>
                        <td><a href="path/to/homework/assignment2.pdf" target="_blank">Assignment 2</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Comment Section -->
        <div class="comment-section">
           
        <button class="add-comment-btn">
    <a href="addcomment.php">Add Comment</a>
</button>

        </div>
    </div>
    
    <script>
        // Toggle Profile Section
        function toggleProfile() {
            const profileSection = document.getElementById('profile-section');
            profileSection.style.display = profileSection.style.display === 'none' ? 'block' : 'none';
        }

        // Switch between tabs
        function showTab(index) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach((tab, i) => {
                tab.classList.remove('active');
                tabContents[i].classList.remove('active');
                if (i === index) {
                    tab.classList.add('active');
                    tabContents[i].classList.add('active');
                }
            });
        }
    </script>

    <!-- footer -->
    <?php include '../footer.php'; ?>
   

</body>
</html>

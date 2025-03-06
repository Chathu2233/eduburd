<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child's Assignment Submission Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/parent/submissionstatus.css">
</head>

<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>
    

    <div class="container">
        <!-- Page Header -->
        <div class="header">
            <h1>Child's Assignment Submission Status</h1>
            <p>Track your child's assignments and their current status here.</p>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Assignment Name</th>
                    <th>Subject</th>
                    <th>Topic</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th>Feedback</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Assignment 1</td>
                    <td>English</td>
                    <td>Nouns</td>
                    <td>20/11/2024</td>
                    <td class="status-pending">Pending</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Assignment 1</td>
                    <td>Maths</td>
                    <td>Constructions</td>
                    <td>15/11/2024</td>
                    <td class="status-submitted">Submitted</td>
                    <td>95%</td>
                    <td>Great job! Keep it up!</td>
                    <td><a href="#" class="download-btn">Download</a></td>
                </tr>
                <tr>
                    <td>Assignment 2</td>
                    <td>English</td>
                    <td>Verbs</td>
                    <td>12/11/2024</td>
                    <td class="status-overdue">Overdue</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

  
         <!-- footer -->
         <?php include '../footer.php'; ?>
   
</body>
<script>
    const downloadButtons = document.querySelectorAll('.download-btn');
    downloadButtons.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            alert('Download feature coming soon!');
        });
    });
</script>
</html>

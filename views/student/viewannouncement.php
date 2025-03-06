<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd Announcements</title>
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="../../assets/css/student/viewannouncement.css">
</head>
<body>

         
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>




    <main>
        <section class="announcements">

        
            <div class="announcement-container">
               <h2>General news and Announcements</h2>
            </div>

            <table>
                <tr>
                    <th>Announcements</th>
                    <th>Posted</th>
                </tr>
                <tr>
                    <td>Test announcement</td>
                    <td>36 minutes ago</td>
                </tr>
                <tr>
                    <td>Test announcement</td>
                    <td>12th July, 2024</td>
                </tr>

                <tr>
                    <td>Test announcement</td>
                    <td>12th July, 2024</td>
                </tr>

                <tr>
                    <td>Test announcement</td>
                    <td>12th July, 2024</td>
                </tr>
            </table>
        </section>
    </main>
</body>
<?php include '../footer.php'; ?>  
</html>

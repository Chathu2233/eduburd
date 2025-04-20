<?php
include '../connect.php';
require_once '../constants.php';
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/mychildlist.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">

</head>
<body>
    <!-- Header -->
    <header>
        <?php include __DIR__ . '/../header_parent.php'; ?>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar1_parent.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h1 class="page-title">My Child List</h1>
                <div class="child-grid">
                    <!-- Example child card -->
                    <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>
                       <!-- Example child card -->
                       <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>
                       <!-- Example child card -->
                       <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>
                       <!-- Example child card -->
                       <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>

                      <!-- Example child card -->
                      <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>

                      <!-- Example child card -->
                      <div class="child-box" id="child-card-1">
                        <div class="child-info">
                            <h3 class="child-name">John Doe</h3>
                            <p class="student-id">Student ID: 12345</p>
                        </div>
                        <div class="child-actions">
                            <a href="eachchild_dashboard.php?student_id=12345" class="btn view-btn">View</a>
                            <button class="btn delete-btn" onclick="deleteChild(12345)">Delete</button>
                        </div>
                    </div>
                    <!-- Add more child cards as needed -->
                </div>
                <a href="parent_send_request.php" class="add-child-btn"> Add Child</a>
            </div>
        </main>
    </div>

    <script>
        function deleteChild(student_id) {
            if (confirm("Are you sure you want to delete this child?")) {
                // Simulate deletion for frontend demo
                document.getElementById("child-card-" + student_id).remove();
                alert("Child deleted successfully!");
            }
        }
    </script>
    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
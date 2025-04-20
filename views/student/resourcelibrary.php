<?php
session_start();
// Include database connection
require_once '../db.php';

// Fetch resources from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM resource_library ORDER BY title ASC");
    $stmt->execute();
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Resource Library</title>
    <link rel="stylesheet" href="../../assets/css/student/resourcelibrary.css">
    <link rel="stylesheet" href="../../assets/css/student/sidebar.css">
</head>
<body>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include 'sidebar.php'; ?>
        </aside>

        <!-- Resource Library Content -->
        <main class="content">
            <section class="resource-section">
                <h1>Resource Library</h1>

                <!-- Add Resource Button -->
                <div class="add-resource-button">
                    <a href="resourceadd.php">
                        <button class="add-resource-btn">+ Add Resource</button>
                    </a>
                </div>

                <!-- Resource Container -->
                <div class="resource-container">
                    <div class="resource-list">
                        <?php if (empty($resources)): ?>
                            <p>No resources found.</p>
                        <?php else: ?>
                            <?php foreach ($resources as $resource): ?>
                                <div class="resource" data-type="<?php echo htmlspecialchars($resource['type']); ?>">
                                    <img src="../../assets/images/<?php echo htmlspecialchars($resource['type']); ?>.png" alt="Resource Icon">
                                    <div class="resource-info">
                                        <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                        <p><?php echo htmlspecialchars($resource['description']); ?></p>
                                        <p>Format: <?php echo htmlspecialchars(ucwords($resource['type'])); ?></p>
                                        <?php if ($resource['file_path']): ?>
                                            <a href="resources/<?php echo htmlspecialchars($resource['file_path']); ?>" download>
                                                <button>Download</button>
                                            </a>
                                        <?php else: ?>
                                            <p>No file available</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <div id="tutorials" class="tab-content">
                <h2>Assignments</h2>
                <?php if (empty($assignments)): ?>
                    <p>No assignments available for this class.</p>
                <?php else: ?>
                    <ul class="assignment-list">
                        <?php foreach ($assignments as $assignment): ?>
                            <li class="assignment-item">
                                <div class="assignment-header">
                                    <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                    <div class="assignment-actions">
                                        <span class="status <?php echo $assignment['is_submitted'] > 0 ? 'submitted' : (date('Y-m-d') > $assignment['deadline'] ? 'closed' : ''); ?>">
                                            <?php if ($assignment['is_submitted'] > 0): ?>
                                                ✔ Submitted
                                            <?php elseif (date('Y-m-d') > $assignment['deadline']): ?>
                                                Submission Closed
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($assignment['is_submitted'] == 0 && date('Y-m-d') <= $assignment['deadline']): ?>
                                            <button class="submit-btn" onclick="window.location.href='submission.php?assignment_id=<?php echo $assignment['assignment_id']; ?>'">Submit</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p><?php echo htmlspecialchars($assignment['description']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($assignment['deadline']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
</body>
<script>
    // Filter resources
    function filterResources() {
        const format = document.getElementById('format').value.toLowerCase();
        const resources = document.querySelectorAll('.resource');

        resources.forEach(resource => {
            const resourceType = resource.getAttribute('data-type').toLowerCase();
            if (format === "all formats" || resourceType === format) {
                resource.style.display = "block";
            } else {
                resource.style.display = "none";
            }
        });
    }
</script>
</html>

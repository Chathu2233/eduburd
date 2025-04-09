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
    <header>
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Resource Library Content -->
        <main class="dashboard">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <p>Homepage &gt; Resource Library</p>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="search" placeholder="Search for resources...">
                <button class="search-btn">🔍</button>
            </div>

            <!-- Filters and Resource Section -->
            <div class="resource-container">
                <!-- Resource Cards -->
                <main class="resource-list">
                    <!-- Add Resource Button -->
                    <div class="add-resource-button">
                        <a href="resourceadd.php">
                            <button class="add-resource-btn">+ Add Resource</button>
                        </a>
                    </div>
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
                    <section>
                        <div class="pagination">
                            <a href="#">&laquo;</a>
                            <a href="#" class="active">1</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">&raquo;</a>
                        </div>
                    </section>
                </main>
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

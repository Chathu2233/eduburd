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
    <title>Resource library</title>
    <link rel="stylesheet" href="../../assets/css/student/resourcelibrary.css">
</head>
<body>
    
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Parent Content -->
        <main class="dashboard">
<section>

<h1>Resource Library</h1>

<!-- Search and Filter Section -->
<!-- Search and Filter Section -->
<div class="search-filter">
    <input type="text" id="searchInput" placeholder="Search by resource name..." onkeyup="filterResourcesByName()">
    <button class="search-btn" onclick="filterResourcesByName()">Search</button>
</div>


<!-- Resource Container -->
<div class="resource-container">
    <div class="resource-list">
        <?php if (empty($resources)): ?>
            <p>No resources found.</p>
        <?php else: ?>
            <?php foreach ($resources as $resource): ?>
                <div class="resource" data-title="<?php echo htmlspecialchars(strtolower($resource['title'])); ?>" data-type="<?php echo htmlspecialchars($resource['type']); ?>">
                    <img 
                        src="../../assets/images/<?php echo htmlspecialchars($resource['type']); ?>.png" 
                        alt="Resource Icon" 
                        onerror="this.onerror=null; this.src='../../assets/images/file.png';">
                    <div class="resource-info">
                        <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                        <p><?php echo htmlspecialchars($resource['description']); ?></p>
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
<!-- Add Resource Button -->
<div class="add-resource-button">
    <a href="resourceadd.php">
        <button class="buttonadd">Add Resource</button>
    </a>
</div>
</section>
        </main>
        </div>

     <!-- Footer -->
     <?php include '../footer.php'; ?>
     <script>
    // Filter resources by name
    function filterResourcesByName() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const resources = document.querySelectorAll('.resource');

        resources.forEach(resource => {
            const resourceTitle = resource.getAttribute('data-title');
            if (resourceTitle.includes(searchInput)) {
                resource.style.display = "block";
            } else {
                resource.style.display = "none";
            }
        });
    }
</script>
     </body>
</html>
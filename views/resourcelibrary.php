<?php
session_start();
require_once 'constants.php';
require_once 'db.php';

// Pagination setup
$limit = 8; // Number of resources per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total resource count
try {
    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM resource_library");
    $totalStmt->execute();
    $totalResources = $totalStmt->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch resources for the current page
try {
    $stmt = $pdo->prepare("SELECT * FROM resource_library ORDER BY title ASC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Calculate total pages
$totalPages = ceil($totalResources / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBurd - Resource Library</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/resourcelibrary.css">
</head>
<body>

<header>
    <?php
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'admin':
                include 'header_admin.php';
                break;
            case 'student':
                include 'header_student.php';
                break;
            case 'tutor':
                include 'header_tutor.php';
                break;
            case 'parent':
                include 'header_parent.php';
                break;
            default:
                include 'header_guest.php';
        }
    } else {
        include 'header_guest.php';
    }
    ?>
</header>

<div class="content-wrapper">
    <div class="breadcrumb">
        <p>Homepage &gt; Resource Library</p>
    </div>

    <div class="search-filter">
    <input type="text" id="searchInput" placeholder="Search resource">
    <button id="searchButton" onclick="filterResources()">
        <img src="<?php echo ROOT; ?>/assets/images/search-icon.png" alt="Search" style="width: 20px; height: 20px;">
    </button>
</div>

    <div class="resource-container">
        <div class="resource-list">
            <?php if (empty($resources)): ?>
                <p>No resources found.</p>
            <?php else: ?>
                <?php foreach ($resources as $resource): ?>
                    <div class="resource" 
                         data-title="<?php echo htmlspecialchars(strtolower($resource['title'])); ?>" 
                         data-type="<?php echo htmlspecialchars(strtolower($resource['type'])); ?>">
                        <img src="<?php echo ROOT; ?>/assets/images/<?php echo htmlspecialchars($resource['type']); ?>.png" 
                             alt="Resource Icon" 
                             onerror="this.onerror=null; this.src='<?php echo ROOT; ?>/assets/images/file.png';">
                        <div class="resource-info">
                            <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                            <p><?php echo htmlspecialchars($resource['description']); ?></p>
                            <p>Format: <?php echo htmlspecialchars(ucwords($resource['type'])); ?></p>
                            <?php if ($resource['file_path']): ?>
                                <a href="<?php echo ROOT; ?>/resources/<?php echo htmlspecialchars($resource['file_path']); ?>" download>
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

    <div class="pagination">
    <?php
    $maxPagesToShow = 10; // Maximum number of pagination links to display
    $startPage = max(1, $page - floor($maxPagesToShow / 2));
    $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

    if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">&raquo;</a>
    <?php endif; ?>
</div>
    </div>
    <script>
   function filterResources() {
       const searchInput = document.getElementById('searchInput').value.toLowerCase();
       const resources = document.querySelectorAll('.resource');

       resources.forEach(resource => {
           const resourceTitle = resource.getAttribute('data-title');
           const matchesSearch = resourceTitle.includes(searchInput);

           if (matchesSearch) {
               resource.style.display = "block";
           } else {
               resource.style.display = "none";
           }
       });
   }

   // Optional: Trigger search on Enter key press
   document.getElementById('searchInput').addEventListener('keypress', function(event) {
       if (event.key === 'Enter') {
           filterResources();
       }
   });
</script>

<?php include 'footer.php'; ?>

</body>
</html>
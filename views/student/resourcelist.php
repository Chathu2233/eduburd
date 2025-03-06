<?php

session_start();

require '../db.php'; // Include database connection


// Default filter values
$title_filter = '';
$type_filter = '';

// Check if filters are applied
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['title'])) {
        $title_filter = $_GET['title'];
    }
    if (isset($_GET['type'])) {
        $type_filter = $_GET['type'];
    }
}

// Prepare SQL query with filters
$sql = "SELECT * FROM resource_library WHERE 1=1";

if ($title_filter != '') {
    $sql .= " AND title LIKE :title";
}

if ($type_filter != '') {
    $sql .= " AND type = :type";
}

$sql .= " ORDER BY created_at DESC"; // Or adjust to your preferred ordering
$stmt = $pdo->prepare($sql);

// Bind parameters if filters are applied
if ($title_filter != '') {
    $stmt->bindValue(':title', "%$title_filter%");
}
if ($type_filter != '') {
    $stmt->bindValue(':type', $type_filter);
}

$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Library</title>
    <link rel="stylesheet" href="../../assets/css/student/resourcelist.css">
</head>
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>
<body>
    <div class="container">
        <h1>Resource Library</h1>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="resourcelist.php">
                <label for="title">Search by Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title_filter); ?>" placeholder="Enter title to filter">
                
                <label for="type">Filter by Type:</label>
                <select id="type" name="type">
                    <option value="">Select Type</option>
                    <option value="document" <?php echo $type_filter == 'document' ? 'selected' : ''; ?>>Document</option>
                    <option value="video" <?php echo $type_filter == 'video' ? 'selected' : ''; ?>>Video</option>
                    <option value="image" <?php echo $type_filter == 'image' ? 'selected' : ''; ?>>Image</option>
                </select>

                <button type="submit">Apply Filters</button>
            </form>
        </div>

        <!-- Display message if there are no resources -->
        <?php if (empty($resources)): ?>
            <p>No resources found matching your filter criteria.</p>
        <?php else: ?>
            <!-- Resources Table -->
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resource['title']); ?></td>
                            <td><?php echo htmlspecialchars($resource['description']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords($resource['type'])); ?></td>
                            <td>
                                <!-- Display link to the file if available -->
                                <?php if ($resource['file_path']): ?>
                                    <a href="uploads/<?php echo htmlspecialchars($resource['file_path']); ?>" download>Download</a>
                                <?php else: ?>
                                    No file uploaded
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <br>
        <!-- Add a Resource Button -->
        <a href="resourceadd.php" class="btn">Add New Resource</a>
    </div>

</body>
<?php include '../footer.php'; ?> 
</html>

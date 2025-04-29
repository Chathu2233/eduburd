<?php
require '../db.php';
require_once '../constants.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id']; // Get logged-in user ID from session

// Handle Add Resource (POST Operation)
if (isset($_POST['add_resource'])) {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['resource_file'];
    $resource_type= $POST['resource_type'];

    // Check if file was uploaded
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_path = '../resources/' . basename($file['name']);
        
       // Ensure the resources directory exists
$resources_dir = realpath(__DIR__ . '/../student/resources');
if (!is_dir($resources_dir)) {
    mkdir($resources_dir, 0777, true); // Create the directory with write permissions
}

// Set the file path
$file_path = $resources_dir . '/' . basename($file['name']);

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Use only the file name for database storage
    $file_name = basename($file['name']);
    $sql = "INSERT INTO resource_library (user_id, title, description, file_path, created_at, resource_type) 
            VALUES (:user_id, :title, :description, :file_path, NOW(), :resource_type)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':title' => $title,
        ':description' => $description,
        ':file_path' => $file_name,
        ':resource_type' => $resource_type
    ]);
    $_SESSION['success_message'] = "Resource added successfully.";
    header("Location: resourceadd.php");
    exit();
} else {
    echo "Error uploading file.";
}
    } else {
        echo "File upload error.";
    }
}

// Handle Delete Resource (POST Operation)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Fetch file path from the database to delete the file
    $sql = "SELECT file_path FROM resource_library WHERE resource_id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resource) {
        // Delete the file from the server
        unlink('resources/' . $resource['file_path']);

        // Delete the resource from the database
        $sql = "DELETE FROM resource_library WHERE resource_id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            header("Location: resourceadd.php");
            exit();
        } catch (PDOException $e) {
            echo "Error deleting resource: " . $e->getMessage();
        }
    } else {
        echo "Resource not found or not owned by user.";
    }
}

// Fetch all resources for the logged-in user
$sql = "SELECT * FROM resource_library WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Child List</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/mychildlist.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/dashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/parent/resourceadd.css">
</head>
<body>
<header>
    <?php include __DIR__ . '/../header_parent.php'; ?>
</header>

<style>
.filter {
    margin-bottom: 20px;
    
}

.filter label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}


.filter select {
    width: 100%; /* Ensures both input and select fields have the same width */
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.filter input {
    width: 95%; /* Ensures both input and select fields have the same width */
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

</style>
<div class="main-layout">
    <?php include __DIR__ . '/sidebar1_parent.php'; ?>

    
        <main class="dashboard">
<section class="form-section">
<h2>Add Resource</h2>

<!-- Display success message -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="success-message">
        <?php 
            echo htmlspecialchars($_SESSION['success_message']); 
            unset($_SESSION['success_message']); // Clear the message after displaying
        ?>
    </div>
<?php endif; ?>
  
<form method="POST" action="resourceadd.php" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" placeholder="Enter resource title" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4" placeholder="Enter a brief description" required></textarea>

    <div>
    <label for="resource_type">Resource Type</label>
    <select id="resource_type" name="resource_type">
        <option value="">-- Select Mode --</option>
        <option value="educational" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'educational') ? 'selected' : ''; ?>>Educational</option>
        
        <option value="novel" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'novel') ? 'selected' : ''; ?>>Novel</option>
        
        <option value="fictional" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'fictional') ? 'selected' : ''; ?>> Fictional </option>
        
        <option value="life lessons" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'life lessons') ? 'selected' : ''; ?>>other</option>
        
    </select>
</div>

    <label for="resource_file">Upload File:</label>
    <input type="file" id="resource_file" name="resource_file" required>

    <button type="submit" name="add_resource">Add Resource</button>
</form>
</section>

 <!-- Sidebar Filters -->
 <aside >
                <h2>Filter resources </h2>
                <form method="GET" action="resourceadd.php">
                    <div class="filter">
                        <label for="experience">Resource type </label>
                       <select id="resource_type" name="resource_type">
        <option value="">-- Select Mode --</option>
        <option value="educational" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'educational') ? 'selected' : ''; ?>>Educational</option>
        
        <option value="novel" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'novel') ? 'selected' : ''; ?>>Novel</option>
        
        <option value="fictional" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'fictional') ? 'selected' : ''; ?>> Fictional </option>
        
        <option value="life lessons" <?php echo (isset($_GET['resource_type']) && $_GET['resource_type'] === 'life lessons') ? 'selected' : ''; ?>>other</option>
        
    </select>
                    <button type="submit" class="filter-btn1" style="background-color:#009688;">Apply Filters</button>
                </form>
            </aside>


<section class="table-section">
        <h2>All Resources</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Resource type</th>
                    <th>File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resources as $resource): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resource['title']); ?></td>
                        <td><?php echo htmlspecialchars($resource['description']); ?></td>
                        <td><?php echo htmlspecialchars($resource['resource_type']); ?></td>
                        <td><a href="resources/<?php echo htmlspecialchars($resource['file_path']); ?>" target="_blank">View File</a></td>
                        <td>
                            <!-- Edit Button -->
                            <a href="resourceedit.php?edit_id=<?php echo $resource['resource_id']; ?>" class="btn edit-btn">Edit</a>
                          
                            <!-- Delete Button -->
                            <form method="POST" action="resourceadd.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $resource['resource_id']; ?>">
                                <button type="submit" class="btn delete-btn" 
                                    onclick="return confirm('Are you sure you want to delete this resource?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <div class="back-button">
                <button class="styled-back-button" onclick="history.back()">Back</button>
            </div>

    </main>
    </div>

 <!-- Footer -->
 <?php include '../footer.php'; ?>
 <script>

</script>
 </body>
</html>
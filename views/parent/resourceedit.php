<?php
require '../db.php';
require_once '../constants.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}
// Handle Edit Resource (Update Operation)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_resource'])) {
    $id = $_POST['resource_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $existing_file = $_POST['existing_file'];

    $file_path = $existing_file; // Default to existing file

    // Handle file upload if a new file is provided
    if (!empty($_FILES['resource_file']['name'])) {
        $file_path = $_FILES['resource_file']['name'];
        $upload_dir = 'resources/';
        $upload_path = $upload_dir . $file_path;

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($_FILES['resource_file']['tmp_name'], $upload_path)) {
            $error_message = "Error uploading the file. Please try again.";
        }
    }

    // Update the database
    $sql = "UPDATE resource_library 
            SET title = :title, 
                description = :description, 
                file_path = :file_path 
            WHERE resource_id = :id";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':file_path' => $file_path,
            ':id' => $id
        ]);
        $_SESSION['success_message'] = "Resource updated successfully!";

        // Redirect back to the resourceadd.php page after a successful update
        header("Location: resourceadd.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error updating resource: " . $e->getMessage();
    }
}

// Fetch the resource to edit
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql = "SELECT * FROM resource_library WHERE resource_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $edit_id]);
    $edit_resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edit_resource) {
        header("Location: resourceadd.php");
        exit();
    }
} else {
    // Redirect back if the edit ID is missing
    header("Location: resourceadd.php");
    exit();
}
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


<div class="main-layout">
    <?php include __DIR__ . '/sidebar1_parent.php'; ?>

    
        <main class="dashboard">

        <section>
<h1>Edit Resource</h1>

<!-- Display success or error messages -->
<?php if (!empty($_SESSION['success_message'])): ?>
    <p class="message success"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
    <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
<?php elseif (!empty($error_message)): ?>
    <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<!-- Edit Resource Form -->
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="resource_id" value="<?php echo htmlspecialchars($edit_resource['resource_id']); ?>">
    <input type="hidden" name="existing_file" value="<?php echo htmlspecialchars($edit_resource['file_path']); ?>">

    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_resource['title']); ?>" required>
    
    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($edit_resource['description']); ?></textarea>

    
    <label for="resource_file">Upload New File (optional):</label>
    <input type="file" id="resource_file" name="resource_file">

    <button type="submit" name="edit_resource">Update Resource</button>
</form>
</section>
        </main>
        </div>

     <!-- Footer -->
     <?php include '../footer.php'; ?>
     <script>
   
</script>
     </body>
</html>
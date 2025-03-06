<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

require '../db.php'; // Include database connection

// Handle Edit Resource (Update Operation)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_resource'])) {
    $id = $_POST['resource_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
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
                type = :type, 
                file_path = :file_path 
            WHERE resource_id = :id";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':type' => $type,
            ':file_path' => $file_path,
            ':id' => $id
        ]);
        $success_message = "Resource updated successfully!";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resource</title>
    <link rel="stylesheet" href="../../assets/css/student/resourceadd.css">
</head>
<header class="navbar">
    <?php include '../header_student.php'; ?>
</header>
<body>
<div class="content">
    <div class="container">
        <h1>Edit Resource</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($success_message)): ?>
            <p class="message success"><?php echo htmlspecialchars($success_message); ?></p>
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
            
            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="document" <?php echo ($edit_resource['type'] == 'document') ? 'selected' : ''; ?>>PDF</option>
                <option value="video" <?php echo ($edit_resource['type'] == 'video') ? 'selected' : ''; ?>>Video</option>
                <option value="image" <?php echo ($edit_resource['type'] == 'image') ? 'selected' : ''; ?>>Image</option>
            </select>

            <label>Existing File:</label>
            <p>
                <?php if (!empty($edit_resource['file_path'])): ?>
                    <?php
                    $file_extension = strtolower(pathinfo($edit_resource['file_path'], PATHINFO_EXTENSION));
                    $file_path = "resources/" . htmlspecialchars($edit_resource['file_path']);
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?php echo $file_path; ?>" alt="Existing File" style="max-width: 200px;">
                    <?php elseif (in_array($file_extension, ['mp4', 'avi'])): ?>
                        <video controls style="max-width: 200px;">
                            <source src="<?php echo $file_path; ?>" type="video/<?php echo $file_extension; ?>">
                        </video>
                    <?php else: ?>
                        <a href="<?php echo $file_path; ?>" target="_blank">View Existing File</a>
                    <?php endif; ?>
                <?php else: ?>
                    <span>No file uploaded previously.</span>
                <?php endif; ?>
            </p>

            <label for="resource_file">Upload New File (optional):</label>
            <input type="file" id="resource_file" name="resource_file">

            <button type="submit" name="edit_resource">Update Resource</button>
        </form>
    </div>
</div>
<?php include '../footer.php'; ?>  
</body>
</html>

<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

require '../db.php'; // Include database connection

$user_id = $_SESSION['user_id']; // Get logged-in user ID from session

// Handle Add Resource (POST Operation)
if (isset($_POST['add_resource'])) {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $grade = $_POST['grade'];
    $course = $_POST['course'];
    $file_path = null;

    if ($type === 'link') {
        // If type is link, get the URL
        $file_path = $_POST['resource_link'];
    } elseif (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
        // If type is file, handle file upload
        $file_path = 'resources/' . basename($_FILES['resource_file']['name']);
        if (!move_uploaded_file($_FILES['resource_file']['tmp_name'], $file_path)) {
            echo "Error uploading file.";
            exit();
        }
    }

    // Insert resource into database
    $sql = "INSERT INTO resource_library (user_id, title, description, type, grade, course, file_path, created_at) 
            VALUES (:user_id, :title, :description, :type, :grade, :course, :file_path, NOW())";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':description' => $description,
            ':type' => $type,
            ':grade' => $grade,
            ':course' => $course,
            ':file_path' => $file_path
        ]);
        echo "Resource added successfully.";
    } catch (PDOException $e) {
        echo "Error adding resource: " . $e->getMessage();
    }
}

// Handle Delete Resource (POST Operation)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Fetch file path from the database to delete the file
    $sql = "SELECT file_path, type FROM resource_library WHERE resource_id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resource && $resource['type'] !== 'link') {
        // Delete the file from the server if it's not a link
        unlink($resource['file_path']);
    }

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Modern Font -->
    <link rel="stylesheet" href="../../assets/css/student/resourceadd.css">
    <script>
        function toggleResourceInput() {
            const type = document.getElementById('type').value;
            const fileInput = document.getElementById('file-input');
            const linkInput = document.getElementById('link-input');

            if (type === 'link') {
                fileInput.style.display = 'none';
                linkInput.style.display = 'block';
            } else {
                fileInput.style.display = 'block';
                linkInput.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <?php
        // Dynamically include the correct header based on user role
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    include '../header_admin.php';
                    break;
                case 'student':
                    include '../header_student.php';
                    break;
                case 'tutor':
                    include '../header_tutor.php';
                    break;
                case 'parent':
                    include '../header_parent.php';
                    break;
                default:
                    include '../header_guest.php'; // Fallback for unknown roles
            }
        } else {
            include '../header_guest.php'; // For guests (not logged in)
        }
        ?>
    </header>
    <div class="content">

    <!-- Main Container -->
    <div class="container">
        <h1>Resource Library</h1>

        <!-- Add Resource Section -->
        <section class="form-section">
            <h2>Add Resource</h2>
            <form method="POST" action="resourceadd.php" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter resource title" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter a brief description" required></textarea>

                <label for="type">Type:</label>
                <select id="type" name="type" onchange="toggleResourceInput()" required>
                    <option value="document">Document</option>
                    <option value="link">Link</option>
                    <option value="image">Image</option>
                </select>

                <div id="file-input">
                    <label for="resource_file">Upload File:</label>
                    <input type="file" id="resource_file" name="resource_file">
                </div>

                <div id="link-input" style="display: none;">
                    <label for="resource_link">Add Link:</label>
                    <input type="url" id="resource_link" name="resource_link" placeholder="Enter resource link">
                </div>

                <label for="grade">Grade:</label>
                <select id="grade" name="grade" required>
                    <option value="">Select Grade</option>
                    <option value="1">Grade 1</option>
                    <option value="2">Grade 2</option>
                    <option value="3">Grade 3</option>
                    <option value="4">Grade 4</option>
                    <option value="5">Grade 5</option>
                    <option value="6">Grade 6</option>
                    <option value="7">Grade 7</option>
                    <option value="8">Grade 8</option>
                    <option value="9">Grade 9</option>
                    <option value="10">Grade 10</option>
                    <option value="11">Grade 11</option>
                    <option value="12">Grade 12</option>
                    <option value="13">Personalised Primary And Lower Secondary Tuition</option>
                    <option value="14">Personalised IGCSE Tuition</option>
                    <option value="15">Personalised A1 & A2 Tuition</option>
                </select>

                <label for="course">Course:</label>
                <input type="text" id="course" name="course" placeholder="Enter course name" required>

                <button type="submit" name="add_resource">Add Resource</button>
            </form>
        </section>

        <!-- Resource Table Section -->
        <section class="table-section">
            <h2>All Resources</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Grade</th>
                        <th>Course</th>
                        <th>File/Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resource['title']); ?></td>
                            <td><?php echo htmlspecialchars($resource['description']); ?></td>
                            <td><?php echo htmlspecialchars($resource['type']); ?></td>
                            <td><?php echo htmlspecialchars($resource['grade']); ?></td>
                            <td><?php echo htmlspecialchars($resource['course']); ?></td>
                            <td>
                                <?php if ($resource['type'] === 'link'): ?>
                                    <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" target="_blank">Visit Link</a>
                                <?php else: ?>
                                    <a href="resources/<?php echo htmlspecialchars($resource['file_path']); ?>" target="_blank">View File</a>
                                <?php endif; ?>
                            </td>
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
    </div>
    </div>
    <?php include '../footer.php'; ?>  
</body>
</html>

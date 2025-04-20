<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit Course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveCourse'])) {
        $course_id = $_POST['course_id'] ?? null;
        $courseName = $_POST['courseName'];
        $courseDescription = $_POST['courseDescription'];
        $grade_ids = $_POST['grade_ids'] ?? []; // Array of selected grade IDs
        $imagePath = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../assets/images/course_photos/";
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $imageName;

            // Move uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "assets/images/course_photos/" . $imageName; // Save relative path
            }
        }

        if (!empty($course_id)) {
            // Update existing course
            $query = "UPDATE course SET name = :name, description = :description, image = :image WHERE course_id = :course_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $courseName,
                ':description' => $courseDescription,
                ':image' => $imagePath,
                ':course_id' => $course_id
            ]);

            // Delete existing grade associations
            $query = "DELETE FROM course_grade WHERE course_id = :course_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':course_id' => $course_id]);
        } else {
            // Insert new course
            $query = "INSERT INTO course (name, description, image) VALUES (:name, :description, :image)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $courseName,
                ':description' => $courseDescription,
                ':image' => $imagePath
            ]);
            $course_id = $pdo->lastInsertId();
        }

        // Insert new grade associations
        foreach ($grade_ids as $grade_id) {
            $query = "INSERT INTO course_grade (course_id, grade_id) VALUES (:course_id, :grade_id)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':course_id' => $course_id,
                ':grade_id' => $grade_id
            ]);
        }

        header('Location: ' . ROOT . '/views/admin/managecourses.php');
        exit();
    }

    // Handle Delete Course
    if (isset($_POST['deleteCourse'])) {
        $course_id = $_POST['course_id'];

        // Fetch the image path to delete the file
        $query = "SELECT image FROM course WHERE course_id = :course_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course && file_exists("../../" . $course['image'])) {
            unlink("../../" . $course['image']); // Delete the image file
        }

        // Delete the course from the database
        $query = "DELETE FROM course WHERE course_id = :course_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);

        header('Location: ' . ROOT . '/views/admin/managecourses.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/admindashboard.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/managecourses.css">
</head>
<body>

<header>
    <?php include '../header_admin.php'; ?>
</header>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Manage Courses</h1>
        <div class="button-container">
            <button onclick="toggleForm()">Add Course</button>
        </div>

        <!-- Course List -->
        <div class="course-list">
            <h2>Course List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Grades</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from the database
                    $query = "SELECT c.course_id, c.name, c.description, c.image, GROUP_CONCAT(g.grade SEPARATOR ', ') AS grades
                              FROM course c
                              LEFT JOIN course_grade cg ON c.course_id = cg.course_id
                              LEFT JOIN grade g ON cg.grade_id = g.grade_id
                              GROUP BY c.course_id";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['course_id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['grades']}</td>
                            <td><img src='" . ROOT . "/{$row['image']}' alt='Course Image' style='width: 50px; height: 50px;'></td>
                            <td>
                                <button onclick=\"editCourse({$row['course_id']}, '{$row['name']}', '{$row['description']}', '{$row['grades']}', '{$row['image']}')\">Edit</button>
                                <form action='' method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this course?');\">
                                    <input type='hidden' name='course_id' value='{$row['course_id']}'>
                                    <button type='submit' name='deleteCourse'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Form -->
        <div id="courseForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Course</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="courseId" name="course_id">
                <input type="text" id="courseName" name="courseName" placeholder="Course Name" required>
                <textarea id="courseDescription" name="courseDescription" placeholder="Description" required></textarea>
                <input type="file" id="image" name="image" accept="image/*">
                <button type="submit" name="saveCourse">Save Course</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function toggleForm() {
        document.getElementById('courseForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Course';
        document.getElementById('courseId').value = '';
        document.getElementById('courseName').value = '';
        document.getElementById('courseDescription').value = '';
        document.getElementById('image').value = '';
    }

    function editCourse(id, name, description, grades, image) {
        document.getElementById('courseForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Course';
        document.getElementById('courseId').value = id;
        document.getElementById('courseName').value = name;
        document.getElementById('courseDescription').value = description;
        // Note: Image input cannot be pre-filled for security reasons
    }
</script>
</body>
</html>

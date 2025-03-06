<?php
// Database connection
include '../db.php';

// Handle Add/Edit Course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveCourse'])) {
        $course_id = $_POST['course_id'] ?? null;
        $courseName = $_POST['courseName'];
        $courseGrade = $_POST['courseGrade'];
        $courseDescription = $_POST['courseDescription'];

        if (!empty($course_id)) {
            // Update existing course
            $query = "UPDATE course SET name = :name, grade = :grade, description = :description WHERE course_id = :course_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $courseName,
                ':grade' => $courseGrade,
                ':description' => $courseDescription,
                ':course_id' => $course_id
            ]);
        } else {
            // Insert new course
            $query = "INSERT INTO course (name, grade, description) VALUES (:name, :grade, :description)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $courseName,
                ':grade' => $courseGrade,
                ':description' => $courseDescription
            ]);
        }

        header('Location: managecourses.php');
        exit();
    }

    // Handle Delete Course
    if (isset($_POST['deleteCourse'])) {
        $course_id = $_POST['course_id'];
        $query = "DELETE FROM course WHERE course_id = :course_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);

        header('Location: managecourses.php');
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
    <link rel="stylesheet" href="/assets/css/admin/managecourses.css">
</head>
<body>

<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>
<div class="manage-container">
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
                    <th>Grade</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch data from the database
                $query = "SELECT * FROM course";
                $stmt = $pdo->prepare($query);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['course_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['grade']}</td>
                        <td>{$row['description']}</td>
                        <td>
                            <button onclick=\"editCourse({$row['course_id']}, '{$row['name']}', '{$row['grade']}', '{$row['description']}')\">Edit</button>
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
        <form action="" method="POST">
            <input type="hidden" id="courseId" name="course_id">
            <input type="text" id="courseName" name="courseName" placeholder="Course Name" required>
            <input type="text" id="courseGrade" name="courseGrade" placeholder="Grade Level" required>
            <textarea id="courseDescription" name="courseDescription" placeholder="Description" required></textarea>
            <button type="submit" name="saveCourse">Save Course</button>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>
        

<script>
    function toggleForm() {
        document.getElementById('courseForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Course';
        document.getElementById('courseId').value = '';
        document.getElementById('courseName').value = '';
        document.getElementById('courseGrade').value = '';
        document.getElementById('courseDescription').value = '';
    }

    function editCourse(id, name, grade, description) {
        document.getElementById('courseForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Course';
        document.getElementById('courseId').value = id;
        document.getElementById('courseName').value = name;
        document.getElementById('courseGrade').value = grade;
        document.getElementById('courseDescription').value = description;
    }
</script>
</body>
</html>

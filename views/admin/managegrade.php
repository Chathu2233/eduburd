<?php
// filepath: c:\xampp\htdocs\eduburd\views\admin\managegrade.php
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit Grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveGrade'])) {
        $grade_id = $_POST['grade_id'] ?? null;
        $grade = $_POST['grade'];
        $imagePath = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../assets/images/grade_photos/";
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $imageName;

            // Move uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "assets/images/grade_photos/" . $imageName; // Save relative path
            }
        }

        if (!empty($grade_id)) {
            // Update existing grade
            $query = "UPDATE grade SET grade = :grade, image = :image WHERE grade_id = :grade_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':grade' => $grade,
                ':image' => $imagePath,
                ':grade_id' => $grade_id
            ]);
        } else {
            // Insert new grade
            $query = "INSERT INTO grade (grade, image) VALUES (:grade, :image)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':grade' => $grade,
                ':image' => $imagePath
            ]);
        }

        header('Location: ' . ROOT . '/views/admin/managegrade.php');
        exit();
    }

    // Handle Delete Grade
    if (isset($_POST['deleteGrade'])) {
        $grade_id = $_POST['grade_id'];

        // Fetch the image path to delete the file
        $query = "SELECT image FROM grade WHERE grade_id = :grade_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':grade_id' => $grade_id]);
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($grade && file_exists("../../" . $grade['image'])) {
            unlink("../../" . $grade['image']); // Delete the image file
        }

        // Delete the grade from the database
        $query = "DELETE FROM grade WHERE grade_id = :grade_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':grade_id' => $grade_id]);

        header('Location: ' . ROOT . '/views/admin/managegrade.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/admin/manageparents.css">
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
        <h1>Manage Grades</h1>
        <div class="button-container">
            <button onclick="toggleForm()">Add Grade</button>
        </div>

        <!-- Grade List -->
        <div class="grade-list">
            <h2>Grade List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Grade ID</th>
                        <th>Grade</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from the database
                    $query = "SELECT grade_id, grade, image FROM grade";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['grade_id']}</td>
                            <td>{$row['grade']}</td>
                            <td><img src='" . ROOT . "/{$row['image']}' alt='Grade Image' style='width: 50px; height: 50px;'></td>
                            <td>
                                <button onclick=\"editGrade({$row['grade_id']}, '{$row['grade']}')\">Edit</button>
                                <form action='' method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this grade?');\">
                                    <input type='hidden' name='grade_id' value='{$row['grade_id']}'>
                                    <button type='submit' name='deleteGrade'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Form -->
        <div id="gradeForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Grade</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="gradeId" name="grade_id">
                <input type="text" id="grade" name="grade" placeholder="Grade Name" required>
                <input type="file" id="image" name="image" accept="image/*">
                <button type="submit" name="saveGrade">Save Grade</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function toggleForm() {
        document.getElementById('gradeForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Grade';
        document.getElementById('gradeId').value = '';
        document.getElementById('grade').value = '';
        document.getElementById('image').value = '';
    }

    function editGrade(id, grade) {
        document.getElementById('gradeForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Grade';
        document.getElementById('gradeId').value = id;
        document.getElementById('grade').value = grade;
    }
</script>

</body>
</html>
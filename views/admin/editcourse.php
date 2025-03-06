<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course Details</title>
    <link rel="stylesheet" href="/assets/css/admin/editcourse.css">
</head>
<header>
    <?php
    include '../header_admin.php';
    ?>
</header>

<body>

<div class="edit-course-container">
    <h1>Edit Course Details</h1>
    
    <form id="editCourseForm">
        <!-- Subject Name -->
        <label for="subjectName">Subject Name:</label>
        <input type="text" id="subjectName" name="subjectName" value="Advanced Mathematics" required>
        
        <!-- Relevant Grades -->
        <label for="relevantGrades">Relevant Grades:</label>
        <input type="text" id="relevantGrades" name="relevantGrades" value="10 - 12" required>
        
        <!-- Description -->
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required>This course covers advanced topics in mathematics, including calculus, algebra, and trigonometry, with a focus on practical applications.</textarea>
        
        <!-- Course Image -->
        <label for="courseImage">Course Image:</label>
        <input type="file" id="courseImage" name="courseImage" accept="image/*">
        
       
        <button type="submit" class="save-button">Save Changes</button>
        </div>

        <!-- Save Changes Button -->
        
    </form>
</div>

<script>
    document.getElementById('editCourseForm').addEventListener('submit', function(event) {
        event.preventDefault();
        alert('Course details have been saved.');
    });
</script>

</body>
</html>

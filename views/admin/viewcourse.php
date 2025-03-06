<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="/assets/css/admin/viewcourse.css">
</head>
<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>
<body>

<div class="course-details-container">
    <h1>Course Details</h1>
    
    <div class="course-info">
        <div class="course-image">
            <img src="course_image_placeholder.jpeg" alt="Subject Image" id="courseImage">
        </div>
        <div class="course-main-details">
            <h2 id="subjectName">Subject Name: </h2>
            <p><strong>Relevant Grades:</strong> <span id="relevantGrades"></span></p>
            <p><strong>Description:</strong> <span id="courseDescription"></span></p> 
            <div class="button1" onclick="window.location.href='editCourse.html';">Edit Details</div>
        </div>
    </div>
    
    <div class="teacher-list">
        <h2>Teachers</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody id="teacherTableBody">
                <!-- Teacher records will be dynamically generated here -->
            </tbody>
        </table>
    </div>
</div>
<?php include '../footer.php'; ?>

<script>
    // Sample teacher data
    const teachers = [
        { name: 'John Doe', email: 'john.doe@example.com', contact: '123-456-7890' },
        { name: 'Jane Smith', email: 'jane.smith@example.com', contact: '987-654-3210' },
    ];

    // Function to display the teacher table
    function displayTeachers() {
        const teacherTableBody = document.getElementById('teacherTableBody');
        teacherTableBody.innerHTML = '';

        teachers.forEach(teacher => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${teacher.name}</td>
                <td>${teacher.email}</td>
                <td>${teacher.contact}</td>
            `;
            teacherTableBody.appendChild(row);
        });
    }

    // Function to get URL parameters
    function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            name: params.get('name') || 'Course Name Not Provided',
            grade: params.get('grade') || 'Grade Not Provided',
            description: params.get('description') || 'Description Not Provided'
        };
    }

    // Display course details from URL parameters
    function displayCourseDetails() {
        const courseDetails = getQueryParams();

        document.getElementById('subjectName').innerText = `Subject Name: ${courseDetails.name}`;
        document.getElementById('relevantGrades').innerText = courseDetails.grade;
        document.getElementById('courseDescription').innerText = courseDetails.description;
    }

    // Initial display of course details and teachers
    displayCourseDetails();
    displayTeachers();
</script>
</body>
</html>

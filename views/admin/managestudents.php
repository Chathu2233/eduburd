<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin/managestudents.css">
</head>
<body>

<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>

<div class="manage-container">
    <h1>Manage Students</h1>
    <div class="button-container">
        <button onclick="openAddStudentForm()">Add Student</button>
    </div>
    
    <div class="student-list">
        <h2>Student List</h2>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <!-- Student records will be dynamically generated here -->
            </tbody>
        </table>
    </div>

    <!-- Add Student Form (hidden initially) -->
    <div id="addStudentForm" style="display: none;">
        <h2>Add New Student</h2>
        <form onsubmit="addStudent(event)">
            <input type="text" id="studentFirstName" placeholder="First Name" required>
            <input type="text" id="studentLastName" placeholder="Last Name" required>
            <input type="email" id="studentEmail" placeholder="Email" required>
            <input type="date" id="studentDOB" placeholder="Date of Birth" required>
            <input type="text" id="studentContact" placeholder="Contact" required>
            
            <button type="submit">Add Student</button>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    // Sample student data
    const students = [
        {
            id: 1,
            studentFirstName: 'John',
            studentLastName: 'Doe',
            studentEmail: 'john@example.com',
            studentDOB: '1990-01-01',
            studentContact: '1234567890'
        },
        {
            id: 2,
            studentFirstName: 'Jsssohn',
            studentLastName: 'Doe',
            studentEmail: 'john@example.com',
            studentDOB: '1990-01-01',
            studentContact: '1234567890'
        }

    ];

    // Function to display the student table
    function displayStudents() {
        const studentTableBody = document.getElementById('studentTableBody');
        studentTableBody.innerHTML = '';

        students.forEach(student => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${student.studentFirstName}</td>
                <td>${student.studentLastName}</td>
                <td>${student.studentEmail}</td>
                <td>${student.studentDOB}</td>
                <td>${student.studentContact}</td>
                <td>
                    <button onclick="window.location.href='myprofile.php'">View Profile</button>

                    <button onclick="if (confirm('Are you sure you want to delete this student?')) deleteStudent(${student.id});">Delete</button>

                    
                </td>
            `;
            studentTableBody.appendChild(row);
        });
    }

    // Add Student
    function addStudent(event) {
        event.preventDefault();
        const name = document.getElementById('studentName').value;
        const email = document.getElementById('studentEmail').value;
        const newStudent = { id: Date.now(), name, email };
        students.push(newStudent);
        displayStudents();
        document.getElementById('addStudentForm').style.display = 'none';
    }

    // Delete Student
    function deleteStudent(id) {
        const index = students.findIndex(student => student.id === id);
        if (index !== -1) {
            students.splice(index, 1);
            displayStudents();
        }
    }

    // View Profile
    function viewProfile(id) {
        const student = students.find(s => s.id === id);
        alert(`Profile of ${student.name}\nEmail: ${student.email}`);
    }

    // Show Add Student Form
    function openAddStudentForm() {
        document.getElementById('addStudentForm').style.display = 'block';
    }

    // Initial display
    displayStudents();

</script>
</body>
</html>

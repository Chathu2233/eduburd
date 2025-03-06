<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin/announcements.css">
</head>
<body>

<<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>

<div class="announcements-container">
    <h1>Announcements</h1>
    
    <!-- Add Announcement Section -->
    <h2>Add Announcement</h2>
    <form id="announcementForm">
        <label for="announcementText">Announcement Text:</label>
        <textarea id="announcementText" name="announcementText" rows="4" required></textarea>
        
        <label for="audience">Select Audience:</label>
        <select id="audience" name="audience" required>
            <option value="students">Students</option>
            <option value="parents">Parents</option>
            <option value="teachers">Teachers</option>
            <option value="all">All</option>
        </select>
        
        <button type="submit" class="add-button">Add Announcement</button>
    </form>
    
    <!-- Announcement History Table -->
    <h2>Announcement History</h2>
    <table>
        <thead>
            <tr>
                <th>Text</th>
                <th>Audience</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="announcementHistory">
            <!-- Announcement records will be dynamically generated here -->
        </tbody>
    </table>
</div>
<?php include '../footer.php'; ?>

<script>
    let announcementHistory = [];

    // Function to add a new announcement
    document.getElementById('announcementForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const text = document.getElementById('announcementText').value;
        const audience = document.getElementById('audience').value;
        const date = new Date().toLocaleString();

        const announcement = { text, audience, date };
        announcementHistory.push(announcement);
        displayAnnouncements();

        document.getElementById('announcementText').value = '';
        document.getElementById('audience').value = 'students';
    });

    // Function to display announcements
    function displayAnnouncements() {
        const announcementTable = document.getElementById('announcementHistory');
        announcementTable.innerHTML = '';

        announcementHistory.forEach((announcement, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${announcement.text}</td>
                <td>${announcement.audience}</td>
                <td>${announcement.date}</td>
                <td>
                    <button onclick="editAnnouncement(${index})">Edit</button>
                    <button onclick="deleteAnnouncement(${index})">Delete</button>
                </td>
            `;
            announcementTable.appendChild(row);
        });
    }

    // Function to delete an announcement
    function deleteAnnouncement(index) {
        if (confirm("Are you sure you want to delete this announcement?")) {
            announcementHistory.splice(index, 1);
            displayAnnouncements();
        }
    }

    // Function to edit an announcement
    function editAnnouncement(index) {
        const announcement = announcementHistory[index];
        
        document.getElementById('announcementText').value = announcement.text;
        document.getElementById('audience').value = announcement.audience;

        // Remove the announcement being edited and re-display
        announcementHistory.splice(index, 1);
        displayAnnouncements();
    }

    // Initial display
    displayAnnouncements();
</script>

</body>
</html>

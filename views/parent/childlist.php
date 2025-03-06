<?php
include '../connect.php';
session_start();

// Assuming parent ID is stored in session
$parent_id = $_SESSION['parent_id'] ?? 11; // Replace 1 with dynamic parent ID

$query = "
    SELECT u.first_name, u.last_name, ps.nick_name, s.student_id 
    FROM parent_student ps
    JOIN student s ON ps.student_id = s.student_id
    JOIN user u ON s.user_id = u.user_id
    WHERE ps.parent_id = '$parent_id'
";

$result = mysqli_query($conn, $query);

$children = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $children[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="../../assets/css/parent/mychildlist.css">
</head>
<body>
    <!-- Header -->
    <header>
        <?php include '../header_parent.php'; ?>
    </header>

    <div class="container">
        <h1 class="page-title">My Child List</h1>
        <div class="child-grid">
            <?php foreach ($children as $child): ?>
                <div class="child-box" id="child-card-<?php echo $child['student_id']; ?>">
                    <img src="../../assets/images/pic-5.jpg" alt="<?php echo $child['first_name']; ?>" class="child-avatar">
                    <div class="child-info">
                        <h3 class="child-name">
                        <a href="eachchild_dashboard.php?student_id=<?php echo $child['student_id']; ?>" class="child-link">
        <?php echo $child['first_name'] . ' ' . $child['last_name']; ?>
    </a>
                            <!-- Delete icon beside full name -->
                            <button class="btn delete-btn fullname-delete" onclick="deleteChild(<?php echo $child['student_id']; ?>)">🗑️</button>
                        </h3>
                        <p class="nickname">
                            Nickname: 
                            <span id="nickname-text-<?php echo $child['student_id']; ?>">
                                <?php echo $child['nick_name'] ?: 'No Nickname'; ?>
                            </span>
                        </p>
                    </div>
                    <!-- Show nickname actions only if a nickname exists -->
                    <div class="child-actions" id="nickname-actions-<?php echo $child['student_id']; ?>" 
                         style="display: <?php echo $child['nick_name'] ? 'block' : 'none'; ?>;">
                        <button class="btn edit-btn" onclick="editNickname(<?php echo $child['student_id']; ?>)">✏️</button>
                        <button class="btn delete-btn" onclick="deleteNickname(<?php echo $child['student_id']; ?>)">🗑️</button>
                    </div>
                    <!-- Add Nickname Button -->
                    <?php if (!$child['nick_name']): ?>
                        <button class="btn add-btn" id="add-nickname-<?php echo $child['student_id']; ?>" onclick="addNickname(<?php echo $child['student_id']; ?>)">➕ Add Nickname</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="parent_send_request.php" class="add-child-btn">➕ Add Child</a>
    </div>

    <script>
        // Add Nickname
        function addNickname(student_id) {
            const nickname = prompt("Enter a nickname:");
            if (nickname) {
                fetch('addnickname.php', {
                    method: 'POST',
                    body: JSON.stringify({ student_id, nickname }),
                    headers: { 'Content-Type': 'application/json' }
                }).then(response => response.text()).then(data => {
                    alert(data); // Show success or error message
                    // Update nickname text
                    document.getElementById("nickname-text-" + student_id).textContent = nickname;
                    // Hide the add nickname button
                    document.getElementById("add-nickname-" + student_id).style.display = "none";
                    // Show nickname actions (Edit and Delete)
                    document.getElementById("nickname-actions-" + student_id).style.display = "block";
                });
            }
        }

        // Edit Nickname
        function editNickname(student_id) {
            const currentNickname = document.getElementById("nickname-text-" + student_id).textContent;
            const newNickname = prompt("Edit nickname:", currentNickname);
            if (newNickname) {
                fetch('editnickname.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ student_id, new_nickname: newNickname })
                }).then(response => response.text()).then(data => {
                    alert(data); // Show success or error message
                    document.getElementById("nickname-text-" + student_id).textContent = newNickname;
                });
            }
        }

        // Delete Nickname
        function deleteNickname(student_id) {
            if (confirm("Are you sure you want to delete the nickname?")) {
                fetch('deletenickname.php', {
                    method: 'POST',
                    body: JSON.stringify({ student_id }),
                    headers: { 'Content-Type': 'application/json' }
                }).then(response => response.text()).then(data => {
                    alert(data); // Show success or error message
                    // Update nickname text
                    document.getElementById("nickname-text-" + student_id).textContent = "No Nickname";
                    // Hide nickname actions
                    document.getElementById("nickname-actions-" + student_id).style.display = "none";
                    // Show the add nickname button
                    document.getElementById("add-nickname-" + student_id).style.display = "block";
                });
            }
        }
       
        async function deleteChild(student_id) {
            if (confirm("Are you sure you want to delete this child?")) {
                const response = await fetch('deletechild.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ student_id })
                });
                const data = await response.text();
                alert(data);
                if (data.includes("successfully")) {
                    document.getElementById("child-card-" + student_id).remove();
                }
            }
        }
   
    </script>
    <!-- footer -->
    <?php include '../footer.php'; ?>
</body>
</html>

<?php
// Database connection
include '../db.php';

// Handle Add/Edit Parent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveParent'])) {
        $parent_id = $_POST['parent_id'] ?? null;
        $parentName = $_POST['parentName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        if (!empty($parent_id)) {
            // Update existing parent
            $query = "UPDATE parent SET name = :name, email = :email, phone = :phone WHERE parent_id = :parent_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $parentName,
                ':email' => $email,
                ':phone' => $phone,
                ':parent_id' => $parent_id
            ]);
        } else {
            // Insert new parent
            $query = "INSERT INTO parent (name, email, phone) VALUES (:name, :email, :phone)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $parentName,
                ':email' => $email,
                ':phone' => $phone
            ]);
        }

        header('Location: manageparents.php');
        exit();
    }

    // Handle Delete Parent
    if (isset($_POST['deleteParent'])) {
        $parent_id = $_POST['parent_id'];
        $query = "DELETE FROM parent WHERE parent_id = :parent_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':parent_id' => $parent_id]);

        header('Location: manageparents.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parents</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin/manageparents.css">
</head>
<body>

<header >
    <?php
    include '../header_admin.php'
    ?>
    </header>

<div class="manage-container">
    <h1>Manage Parents</h1>
    <div class="button-container">
        <button onclick="toggleForm()">Add Parent</button>
    </div>

    <!-- Parent List -->
    <div class="parent-list">
        <h2>Parent List</h2>
        <table>
            <thead>
                <tr>
                    <th>Parent ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>NIC</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch data from the database
                $query = "SELECT user.first_name, user.last_name, user.email, user.contact_no, parent.parent_id, parent.nic
          FROM parent
          JOIN user ON parent.user_id = user.user_id";
$stmt = $pdo->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['parent_id']}</td>
            <td>{$row['first_name']}</td>
            <td> {$row['last_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['contact_no']}</td>
            <td>{$row['nic']}</td>
            <td>
                <button onclick=\"editParent({$row['parent_id']}, '{$row['first_name']}', '{$row['last_name']}', '{$row['email']}', '{$row['contact_no']}', '{$row['nic']}')\">Edit</button>
                <form action='' method='POST' style='display:inline;'>
                    <input type='hidden' name='parent_id' value='{$row['parent_id']}'>
                    <button type='submit' name='deleteParent'>Delete</button>
                </form>
            </td>
        </tr>";
}

                ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Form -->
    <div id="parentForm" class="form-container" style="display: none;">
        <h2 id="formTitle">Add New Parent</h2>
        <form action="" method="POST">
            <input type="hidden" id="parentId" name="parent_id">
            <input type="text" id="parentFirstName" name="parentFirstName" placeholder="First Name" required>
            <input type="text" id="parentLastName" name="parentlastName" placeholder="Last Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
            
            <button type="submit" name="saveParent">Save Parent</button>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>
<script>
    function toggleForm() {
        document.getElementById('parentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Parent';
        document.getElementById('parentId').value = '';
        document.getElementById('parentName').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
    }

    function editParent(id, name, email, phone) {
        document.getElementById('parentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Parent';
        document.getElementById('parentId').value = id;
        document.getElementById('parentName').value = name;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone;
    }
</script>

</body>
</html>

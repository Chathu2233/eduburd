<?php
// Database connection
include '../db.php';
require_once '../constants.php';

// Handle Add/Edit Parent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveParent'])) {
        $parent_id = $_POST['parent_id'] ?? null;
        $parentFirstName = $_POST['parentFirstName'];
        $parentLastName = $_POST['parentLastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $nic = $_POST['nic'];

        if (!empty($parent_id)) {
            // Update existing parent
            $query = "UPDATE user u
                      INNER JOIN parent p ON u.user_id = p.user_id
                      SET u.first_name = :first_name, u.last_name = :last_name, u.email = :email, u.contact_no = :contact_no, p.nic = :nic
                      WHERE p.parent_id = :parent_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':first_name' => $parentFirstName,
                ':last_name' => $parentLastName,
                ':email' => $email,
                ':contact_no' => $phone,
                ':nic' => $nic,
                ':parent_id' => $parent_id
            ]);
        } else {
            // Insert new parent
            $pdo->beginTransaction();
            try {
                // Insert into user table
                $query = "INSERT INTO user (first_name, last_name, email, contact_no, user_role) VALUES (:first_name, :last_name, :email, :contact_no, 'parent')";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':first_name' => $parentFirstName,
                    ':last_name' => $parentLastName,
                    ':email' => $email,
                    ':contact_no' => $phone
                ]);
                $user_id = $pdo->lastInsertId();

                // Insert into parent table
                $query = "INSERT INTO parent (user_id, nic) VALUES (:user_id, :nic)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':nic' => $nic
                ]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        }

        header('Location: ' . ROOT . '/views/admin/manageparents.php');
        exit();
    }

    // Handle Delete Parent
    if (isset($_POST['deleteParent'])) {
        $parent_id = $_POST['parent_id'];
        $query = "DELETE p, u FROM parent p
                  INNER JOIN user u ON p.user_id = u.user_id
                  WHERE p.parent_id = :parent_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':parent_id' => $parent_id]);

        header('Location: ' . ROOT . '/views/admin/manageparents.php');
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
                            <td>{$row['last_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['contact_no']}</td>
                            <td>{$row['nic']}</td>
                            <td>
                                <button onclick=\"editParent({$row['parent_id']}, '{$row['first_name']}', '{$row['last_name']}', '{$row['email']}', '{$row['contact_no']}', '{$row['nic']}')\">Edit</button>
                                <form action='' method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this parent?');\">
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
                <input type="text" id="parentLastName" name="parentLastName" placeholder="Last Name" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
                <input type="text" id="nic" name="nic" placeholder="NIC" required>
                <button type="submit" name="saveParent">Save Parent</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
    function toggleForm() {
        document.getElementById('parentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Add New Parent';
        document.getElementById('parentId').value = '';
        document.getElementById('parentFirstName').value = '';
        document.getElementById('parentLastName').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('nic').value = '';
    }

    function editParent(id, firstName, lastName, email, phone, nic) {
        document.getElementById('parentForm').style.display = 'block';
        document.getElementById('formTitle').innerText = 'Edit Parent';
        document.getElementById('parentId').value = id;
        document.getElementById('parentFirstName').value = firstName;
        document.getElementById('parentLastName').value = lastName;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone;
        document.getElementById('nic').value = nic;
    }
</script>

</body>
</html>

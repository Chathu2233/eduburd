'Y-m-d');
$month = date('F');
$method = 'Card';

// Insert into payment table
$stmt = $pdo->prepare("INSERT INTO payment (grade_class_id, amount, date, method, month) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$grade_class_id, $amount, $today, $method, $month]);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment success</title>
    <link rel="stylesheet" href="../../assets/css/student/success.css">
</head>
<body>
    
    <!-- Header Section -->
    <header class="navbar">
        <?php include '../header_student.php'; ?>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Parent Content -->
        <main class="dashboard">
<section>

<div class="card">
    <div class="checkmark">&#10004;</div>
    <h1>Payment Successful</h1>
    <p>Thank you! Your payment has been processed successfully.</p>
    
       
</section>


        </main>
        </div>

     <!-- Footer -->
     <?php include '../footer.php'; ?>

     </body>
</html>
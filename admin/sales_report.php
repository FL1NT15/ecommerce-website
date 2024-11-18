<?php
include '../components/connect.php';
session_start();

// Check admin authentication
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Retrieve sales data from the database
$sales_query = $conn->prepare("SELECT product_name, quantity, total_price, placed_on FROM orders ORDER BY placed_on DESC");
$sales_query->execute();
$sales_data = $sales_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/sidebar.css"> <!-- Link to sidebar.css -->
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .report-container { width: 90%; margin: auto; }
        h2 { text-align: center; }
        .print-button { margin-bottom: 20px; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
    </style>
</head>

<?php include '../components/sidebar.php'; ?> <!-- Include sidebar.php -->
<body>
    <div class="main">
    <h1 class="Sales"> Sales Report</h2>

    <div class="report-container">
  
        <div class="print-button">
            <button onclick="window.print()">Print Report</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Price</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data as $sale): ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['product_name']); ?></td>
                        <td><?= htmlspecialchars($sale['quantity']); ?></td>
                        <td><?= htmlspecialchars($sale['total_price']); ?></td>
                        <td><?= htmlspecialchars($sale['placed_on']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

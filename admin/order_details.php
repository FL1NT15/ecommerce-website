<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Check if order_id is provided in the URL
if (!isset($_GET['order_id'])) {
    header('location: placed_orders.php');
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details
$order_query = "SELECT * FROM orders WHERE id = :order_id";
$order_stmt = $conn->prepare($order_query);
$order_stmt->execute(['order_id' => $order_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('location: placed_orders.php');
    exit;
}

// Check if 'order_items' column exists and is not null or empty
$order_items = isset($order['order_items']) ? json_decode($order['order_items'], true) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Order #<?= $order['id'] ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<!-- Order Details Section -->
<div class="container mt-5">
    <h2>Order Details - Order #<?= $order['id'] ?></h2>
    <div class="card">
        <div class="card-header">Order Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= $order['name'] ?></p>
            <p><strong>Email:</strong> <?= $order['email'] ?></p>
            <p><strong>Address:</strong> <?= $order['address'] ?></p>
            <p><strong>Total Products:</strong> <?= $order['total_products'] ?></p>
            <p><strong>Total Price:</strong> $<?= number_format($order['total_price'], 2) ?></p>
            <p><strong>Payment Status:</strong> <?= ucfirst($order['payment_status']) ?></p>
            <p><strong>Placed On:</strong> <?= $order['placed_on'] ?></p>
        </div>
    </div>

    <h3 class="mt-4">Order Items</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($order_items && is_array($order_items)): ?>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No items found for this order.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="placed_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

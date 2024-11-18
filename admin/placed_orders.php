<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Fetch Recent Orders from the database
$order_query = "SELECT * FROM orders ORDER BY placed_on DESC LIMIT 10"; // You can adjust the LIMIT for the number of recent orders
$order_stmt = $conn->prepare($order_query);
$order_stmt->execute();
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process Order Status Update
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    $new_status = $_POST['new_status'];
    $order_id = $_POST['order_id'];

    $update_query = "UPDATE orders SET payment_status = :new_status WHERE id = :order_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute(['new_status' => $new_status, 'order_id' => $order_id]);

    // Redirect to avoid resubmission on page refresh
    header('location: placed_orders.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/sidebar.css"> <!-- Link to sidebar.css -->
</head>
<body>

<?php include '../components/sidebar.php'; ?> <!-- Include sidebar.php -->

<!-- Main Content -->
<div class="main">
    <h2 class="heading">Order Management</h2>

<!-- Order Management Section -->
<section>
    <div class="card">
        <div class="card-header">Recent Orders</div>
        <div class="card-body">
            <ul class="list-group">
                <?php foreach ($orders as $order): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            Order #<?= $order['id'] ?> - 
                            <span class="text-<?= getOrderStatusClass($order['payment_status']) ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </div>

                        <div>
                            <!-- Update Status Form -->
                            <form method="POST" class="d-inline ml-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="new_status" class="form-control-sm d-inline" style="width: 120px;">
                                    <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="shipped" <?= $order['payment_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['payment_status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="canceled" <?= $order['payment_status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                                    <option value="approved" <?= $order['payment_status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $order['payment_status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary ml-2">Update</button>
                                <a href="order_details.php?order_id=<?= $order['id'] ?>" class="btn btn-link">View</a>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="return_request.php" class="btn btn-secondary mt-2">Return/Refund Requests</a>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Helper function to get status class for color coding
function getOrderStatusClass($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'shipped': return 'info';
        case 'delivered': return 'success';
        case 'canceled': return 'danger';
        case 'rejected': return 'danger';
        case 'approved': return 'success';
        default: return 'secondary';
    }
}
?>
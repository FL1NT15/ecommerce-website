<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Fetch return/refund requests (assuming we're using the `orders` table)
$order_query = "SELECT * FROM orders WHERE payment_status IN ('return request', 'approved request', 'rejected request', 'return request', 'refund request') ORDER BY placed_on DESC";
$order_stmt = $conn->prepare($order_query);
$order_stmt->execute();
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process return request approval or rejection
if (isset($_POST['approve_request']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = 'approved'; // Set this to your desired status

    $update_query = "UPDATE orders SET payment_status = :status WHERE id = :order_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute(['status' => $status, 'order_id' => $order_id]);

    // Redirect to avoid resubmission
    header('location: return_request.php');
    exit;
} elseif (isset($_POST['reject_request']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = 'rejected'; // Set this to your desired status

    $update_query = "UPDATE orders SET payment_status = :status WHERE id = :order_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute(['status' => $status, 'order_id' => $order_id]);

    // Redirect to avoid resubmission
    header('location: return_request.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return/Refund Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<!-- Return/Refund Requests Section -->
<div class="container mt-5">
    <h2>Return/Refund Requests</h2>
    <div class="card">
        <div class="card-header">Requests List</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= $order['name'] ?></td>
                            <td>
                                <span class="badge <?= $order['payment_status'] == 'approved request' ? 'badge-success' : ($order['payment_status'] == 'rejected request' ? 'badge-danger' : 'badge-warning') ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </td>
                            <td>
                            <?php if (in_array($order['payment_status'], ['Return Request', 'Refund Request'])): ?>
    <form method="POST" class="d-inline">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <button type="submit" name="approve_request" class="btn btn-sm btn-success">Approve</button>
    </form>
    <form method="POST" class="d-inline">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <button type="submit" name="reject_request" class="btn btn-sm btn-danger">Reject</button>
    </form>
<?php else: ?>
    <span class="text-muted">No action</span>
<?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="placed_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

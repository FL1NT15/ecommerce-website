<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Initialize default date range
$from_date = $_POST['from_date'] ?? date('Y-m-01'); // Start of current month
$to_date = $_POST['to_date'] ?? date('Y-m-d'); // Today's date

// Initialize variables
$totalSales = 0;
$orderCount = 0;
$pendingOrders = 0;
$deliveredOrders = 0;
$approvedOrders = 0;
$rejectedOrders = 0;
$canceledOrders = 0;
$shippedOrders = 0;

// Fetch Total Sales (only for delivered orders)
$sales_query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE placed_on BETWEEN :from_date AND :to_date AND payment_status = 'delivered'";
$sales_stmt = $conn->prepare($sales_query);
$sales_stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
$result = $sales_stmt->fetch(PDO::FETCH_ASSOC);

if ($result && isset($result['total_sales'])) {
    $totalSales = $result['total_sales'];
} else {
    $totalSales = 0;
}

// Fetch Order Count and Status (including return and refund)
$order_query = "SELECT payment_status, COUNT(*) AS count FROM orders WHERE placed_on BETWEEN :from_date AND :to_date GROUP BY payment_status";
$order_stmt = $conn->prepare($order_query);
$order_stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
while ($row = $order_stmt->fetch(PDO::FETCH_ASSOC)) {
    $orderCount += $row['count'];
    if ($row['payment_status'] == 'pending') $pendingOrders = $row['count'];
    if ($row['payment_status'] == 'delivered') $deliveredOrders = $row['count'];
    if ($row['payment_status'] == 'canceled') $canceledOrders = $row['count'];
    if ($row['payment_status'] == 'approved') $approvedOrders = $row['count'];
    if ($row['payment_status'] == 'rejected') $rejectedOrders = $row['count'];
    if ($row['payment_status'] == 'shipped') $shippedOrders = $row['count'];
   
}

// Prepare data for Daily Sales Performance (last 7 days)
$daily_sales_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $daily_sales_query = "SELECT SUM(total_price) AS daily_sales FROM orders WHERE placed_on = :date AND payment_status = 'delivered'";
    $daily_sales_stmt = $conn->prepare($daily_sales_query);
    $daily_sales_stmt->execute(['date' => $date]);
    $daily_sales_data[] = $daily_sales_stmt->fetch(PDO::FETCH_ASSOC)['daily_sales'] ?? 0;
}

// Prepare data for Weekly Sales Performance (last 4 weeks)
$weekly_sales_data = [];
for ($i = 3; $i >= 0; $i--) {
    $start_date = date('Y-m-d', strtotime("this week -$i week"));
    $end_date = date('Y-m-d', strtotime("last day of this week -$i week"));
    $weekly_sales_query = "SELECT SUM(total_price) AS weekly_sales FROM orders WHERE placed_on BETWEEN :start_date AND :end_date AND payment_status = 'delivered'";
    $weekly_sales_stmt = $conn->prepare($weekly_sales_query);
    $weekly_sales_stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $weekly_sales_data[] = $weekly_sales_stmt->fetch(PDO::FETCH_ASSOC)['weekly_sales'] ?? 0;
}

// Prepare data for Monthly Sales Performance (last 6 months)
$monthly_sales_data = [];
for ($i = 5; $i >= 0; $i--) {
    $start_date = date('Y-m-01', strtotime("-$i month"));
    $end_date = date('Y-m-t', strtotime("-$i month"));
    $monthly_sales_query = "SELECT SUM(total_price) AS monthly_sales FROM orders WHERE placed_on BETWEEN :start_date AND :end_date AND payment_status = 'delivered'";
    $monthly_sales_stmt = $conn->prepare($monthly_sales_query);
    $monthly_sales_stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $monthly_sales_data[] = $monthly_sales_stmt->fetch(PDO::FETCH_ASSOC)['monthly_sales'] ?? 0;
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
    <h1 class="heading">Admin Dashboard</h1>

    <!-- Sales Overview -->
    <section>
        <h2>Sales Overview</h2>
        <div class="row">
            <!-- Total Sales Card -->
            <div class="col-md-6">
                <div class="card">
                <div class="card-header text-left">Total Sales</div>
                    <div class="card-body text-left">
                        <h5 class="card-title">Total: ₱<?= number_format($totalSales, 2); ?></h5>
                        <form method="post">
                            <label>From: <input type="date" name="from_date" class="form-control d-inline w-100" value="<?= $from_date; ?>"></label>
                            <label>To: <input type="date" name="to_date" class="form-control d-inline w-100" value="<?= $to_date; ?>"></label>
                            <button type="submit" class="btn btn-primary mt-0">Filter</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Number of Orders Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-left">Number of Orders</div>
                    <div class="card-body text-left">
                        <h5 class="card-title">Total Orders: <?= $orderCount; ?></h5>
                        <p>Pending: <?= $pendingOrders; ?></p>
                        <p>Delivered: <?= $deliveredOrders; ?></p>
                        <p>Canceled: <?= $canceledOrders; ?></p>
                        <p>Shipped: <?= $shippedOrders; ?></p>
                        <p>Approved Return/Refund: <?= $approvedOrders; ?></p>
                        <p>Rejected Return/Refund: <?= $rejectedOrders; ?></p>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Daily, Weekly, Monthly Sales -->
    <section>
        <h2>Sales Performance</h2>
        <!-- Daily Sales Chart -->
        <h4>Daily Sales</h4>
        <div>
            <canvas id="dailySalesChart"></canvas>
        </div>

        <!-- Weekly Sales Chart -->
        <h4>Weekly Sales</h4>
        <div>
            <canvas id="weeklySalesChart"></canvas>
        </div>

        <!-- Monthly Sales Chart -->
        <h4>Monthly Sales</h4>
        <div>
            <canvas id="monthlySalesChart"></canvas>
        </div>
        
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['7 days ago', '6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', '1 day ago'],
            datasets: [{
                label: 'Daily Sales (₱)',
                data: <?= json_encode($daily_sales_data); ?>,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        }
    });

    var ctx2 = document.getElementById('weeklySalesChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Weekly Sales (₱)',
                data: <?= json_encode($weekly_sales_data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    var ctx3 = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: ['Month 1', 'Month 2', 'Month 3', 'Month 4', 'Month 5', 'Month 6'],
            datasets: [{
                label: 'Monthly Sales (₱)',
                data: <?= json_encode($monthly_sales_data); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        }
    });
</script>

</body>
</html>

<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
   exit();
}

// Fetch the role of the logged-in admin
$select_admin = $conn->prepare("SELECT role_id FROM admins WHERE id = ?");
$select_admin->execute([$admin_id]);
$fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);

if ($fetch_admin) {
    $role_id = $fetch_admin['role_id'];
    $_SESSION['role_id'] = $role_id;
} else {
    session_destroy();
    header('location:admin_login.php');
    exit();
}

// Fetch total number of customers
$total_customers_query = $conn->prepare("SELECT COUNT(*) AS total_customers FROM users");
$total_customers_query->execute();
$total_customers = $total_customers_query->fetch(PDO::FETCH_ASSOC)['total_customers'];

// Fetch new customers (daily)
$today = date('Y-m-d');
$new_customers_daily_query = $conn->prepare("SELECT COUNT(*) AS new_customers_daily FROM users WHERE DATE(created_at) = ?");
$new_customers_daily_query->execute([$today]);
$new_customers_daily = $new_customers_daily_query->fetch(PDO::FETCH_ASSOC)['new_customers_daily'];

// Fetch new customers (weekly)
$week_start = date('Y-m-d', strtotime('-7 days'));
$new_customers_weekly_query = $conn->prepare("SELECT COUNT(*) AS new_customers_weekly FROM users WHERE DATE(created_at) BETWEEN ? AND ?");
$new_customers_weekly_query->execute([$week_start, $today]);
$new_customers_weekly = $new_customers_weekly_query->fetch(PDO::FETCH_ASSOC)['new_customers_weekly'];

// Fetch returning customers
$returning_customers_query = $conn->prepare("SELECT COUNT(*) AS returning_customers FROM orders GROUP BY user_id HAVING COUNT(*) > 1");
$returning_customers_query->execute();
$returning_customers = $returning_customers_query->rowCount();

// Fetch all users for the table
$users_query = $conn->prepare("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
$users_query->execute();
$users = $users_query->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<!-- Main Content -->
<div class="main">
    <h2 class="heading">Customers</h2>

    <section class="customer-summary">
       <div class="container">
           <div class="row">
               <div class="col-md-6">
                   <div class="card">
                       <div class="card-body">
                           <h5 class="card-title">Total Customers</h5>
                           <p class="card-text"><?= $total_customers; ?></p>
                       </div>
                   </div>
               </div>
               <div class="col-md-6">
                   <div class="card">
                       <div class="card-body">
                           <h5 class="card-title">New Customers (Daily)</h5>
                           <p class="card-text"><?= $new_customers_daily; ?></p>
                       </div>
                   </div>
               </div>
               <div class="col-md-6">
                   <div class="card">
                       <div class="card-body">
                           <h5 class="card-title">New Customers (Weekly)</h5>
                           <p class="card-text"><?= $new_customers_weekly; ?></p>
                       </div>
                   </div>
               </div>
               <div class="col-md-6">
                   <div class="card">
                       <div class="card-body">
                           <h5 class="card-title">Returning Customers</h5>
                           <p class="card-text"><?= $returning_customers; ?></p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
    </section>

    <!-- User Information Table -->
    <section class="user-table mt-5">
       <div class="container">
           <h3>Customer Information</h3>
           <div class="table-responsive">
               <table class="table table-bordered table-striped">
                   <thead class="thead-dark">
                       <tr>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Registration Date</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($users as $user): ?>
                           <tr>
                               <td><?= htmlspecialchars($user['id']); ?></td>
                               <td><?= htmlspecialchars($user['name']); ?></td>
                               <td><?= htmlspecialchars($user['email']); ?></td>
                               <td><?= htmlspecialchars($user['created_at']); ?></td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               </table>
           </div>
       </div>
    </section>
</div>

<script src="../js/admin_script.js"></script>
   
</body>
</html>

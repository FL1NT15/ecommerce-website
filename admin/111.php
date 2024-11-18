 <!-- Order Management -->
 <section>
        <h2>Order Management</h2>
        <div class="card">
            <div class="card-header">Recent Orders</div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">Order #1234 - <span class="text-warning">Pending</span> <a href="#" class="btn btn-sm btn-primary float-right">Process</a></li>
                    <li class="list-group-item">Order #5678 - <span class="text-success">Shipped</span> <a href="#" class="btn btn-sm btn-primary float-right">View</a></li>
                    <li class="list-group-item">Order #9101 - <span class="text-danger">Canceled</span> <a href="#" class="btn btn-sm btn-primary float-right">View</a></li>
                </ul>
                <a href="return_requests.php" class="btn btn-secondary mt-2">Return/Refund Requests</a>
            </div>
        </div>
    </section>

    <!-- Customers -->
    <section>
        <h2>Customers</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Total Customers</div>
                    <div class="card-body">
                        <h5 class="card-title">Total: X</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">New Customers</div>
                    <div class="card-body">
                        <h5 class="card-title">Weekly: Y</h5>
                        <h5 class="card-title">Daily: Z</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Returning Customers</div>
                    <div class="card-body">
                        <h5 class="card-title">Total: A</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Management -->
    <section>
        <h2>Product Management</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Inventory Status</div>
                    <div class="card-body">
                        <p>Low Stock Alerts: <span class="text-danger">3 products</span></p>
                        <p>Out of Stock: <span class="text-danger">5 products</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Best Selling Products</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">Product A</li>
                            <li class="list-group-item">Product B</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">New Products</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">Product C</li>
                            <li class="list-group-item">Product D</li>
                        </ul>
                        <a href="add_product.php" class="btn btn-primary mt-2">Add New Product</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marketing and Promotions -->
    <section>
        <h2>Marketing and Promotions</h2>
        <div class="card">
            <div class="card-header">Manage Promotions</div>
            <div class="card-body">
                <a href="add_promotion.php" class="btn btn-primary">Add Promotion</a>
                <a href="edit_promotions.php" class="btn btn-secondary">Edit Promotions</a>
                <a href="remove_promotions.php" class="btn btn-danger">Remove Promotions</a>
            </div>
        </div>
    </section>

    <!-- Reports -->
    <section>
        <h2>Reports</h2>
        <div class="card">
            <div class="card-header">Generate Reports</div>
            <div class="card-body">
                <a href="sales_report.php" class="btn btn-primary">Sales Report</a>
                <a href="product_report.php" class="btn btn-secondary">Product Report</a>
            </div>
        </div>
    </section>

    <!-- User Role Management -->
    <section>
        <h2>User Role Management</h2>
        <div class="card">
            <div class="card-header">Admin Account Management</div>
            <div class="card-body">
                <a href="admin_accounts.php" class="btn btn-primary">Manage Admin Accounts</a>
                <a href="user_roles.php" class="btn btn-secondary">User Roles Overview</a>
                <a href="activity_logs.php" class="btn btn-info">View Activity Logs</a>
            </div>
        </div>
    </section>
</div>

<div class="main">
    <h1 class="heading">Admin Dashboard</h1>

    <!-- Sales Overview -->
    <section>
        <h2>Sales Overview</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Total Sales</div>
                    <div class="card-body">
                        <h5 class="card-title">Total: ₱<?= number_format($totalSales, 2); ?></h5>
                        <form method="post">
                            <label>From: <input type="date" name="from_date" class="form-control d-inline w-100" value="<?= $from_date; ?>"></label>
                            <label>To: <input type="date" name="to_date" class="form-control d-inline w-100" value="<?= $to_date; ?>"></label>
                            <button type="submit" class="btn btn-primary mt--1">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
    <div class="card">
        <div class="card-header">Number of Orders</div>
        <div class="card-body">
            <h5 class="card-title">Orders: <?= $orderCount; ?></h5>
            <p>Pending: <?= $pendingOrders; ?></p>
            <p>Delivered: <?= $deliveredOrders; ?></p>
        </div>
    </div>
</div>
    </section>
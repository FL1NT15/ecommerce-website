<?php
// Connect to the database
include '../components/connect.php';

// Start session and check admin authentication
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Messages array for feedback
$message = [];

// Handle adding a new promotion
if (isset($_POST['add_promotion'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $discount_percentage = filter_var($_POST['discount_percentage'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Insert promotion into the database
    $stmt = $conn->prepare("INSERT INTO promotions (name, description, discount_percentage, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $discount_percentage, $start_date, $end_date]);
    $message[] = 'Promotion added successfully!';
}

// Handle deletion of a promotion
if (isset($_GET['delete_promotion'])) {
    $promotion_id = $_GET['delete_promotion'];
    $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt->execute([$promotion_id]);
    header('Location: promotions.php');
    exit();
}

// Fetch all promotions
$promotions = $conn->prepare("SELECT * FROM promotions ORDER BY created_at DESC");
$promotions->execute();
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
<style>

    .main {
    padding: 20px;
    background-color: #f4f4f9;
    font-family: Arial, sans-serif;
}

/* Header styling */
.main h2 {
    color: #333;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

/* Message styling */
.message {
    background-color: #e0f7fa;
    color: #00796b;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #00796b;
    border-radius: 5px;
}

/* Form section styling */
.add-promotion {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.add-promotion h3 {
    margin-bottom: 20px;
    color: #00796b;
}

.add-promotion input[type="text"],
.add-promotion input[type="number"],
.add-promotion input[type="date"],
.add-promotion textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.add-promotion input[type="submit"] {
    background-color: #00796b;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}

.add-promotion input[type="submit"]:hover {
    background-color: #005c4b;
}

/* Table styling */
.promotion-list {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.promotion-list h3 {
    margin-bottom: 20px;
    color: #00796b;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.table thead th {
    background-color: #00796b;
    color: #fff;
    padding: 10px;
    text-align: left;
    font-weight: bold;
}

.table tbody td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Button styling */
.btn {
    padding: 5px 10px;
    border-radius: 3px;
    text-decoration: none;
    color: #fff;
    font-size: 14px;
    margin-right: 5px;
}

.btn-warning {
    background-color: #ffa726;
}

.btn-warning:hover {
    background-color: #fb8c00;
}

.btn-danger {
    background-color: #ef5350;
}

.btn-danger:hover {
    background-color: #d32f2f;
}
</style>

</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main">
<h2 class="heading">Marketing and Promotions</h2>

    <!-- Display feedback messages -->
    <?php if (!empty($message)): ?>
        <div class="message">
            <?php foreach ($message as $msg): ?>
                <p><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form to Add a New Promotion -->
    <section class="add-promotion">
        <h3>Add New Promotion</h3>
        <form action="" method="POST">
            <input type="text" name="name" placeholder="Promotion Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="discount_percentage" placeholder="Discount Percentage" step="0.01" required>
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" required>
            <input type="submit" name="add_promotion" value="Add Promotion">
        </form>
    </section>

    <!-- List of Existing Promotions -->
    <section class="promotion-list">
        <h3>Current Promotions</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Discount (%)</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($promotion = $promotions->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($promotion['name']); ?></td>
                        <td><?= htmlspecialchars($promotion['description']); ?></td>
                        <td><?= htmlspecialchars($promotion['discount_percentage']); ?>%</td>
                        <td><?= htmlspecialchars($promotion['start_date']); ?></td>
                        <td><?= htmlspecialchars($promotion['end_date']); ?></td>
                        <td>
                            <!-- Edit and Delete buttons -->
                            <a href="edit_promotion.php?id=<?= $promotion['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="promotions.php?delete_promotion=<?= $promotion['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this promotion?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>

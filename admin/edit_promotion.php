<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

$message = [];

if (isset($_POST['update_promotion'])) {
    $promotion_id = $_POST['id'];
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $discount_percentage = filter_var($_POST['discount_percentage'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE promotions SET name = ?, description = ?, discount_percentage = ?, start_date = ?, end_date = ? WHERE id = ?");
    $stmt->execute([$name, $description, $discount_percentage, $start_date, $end_date, $promotion_id]);
    $message[] = 'Promotion updated successfully!';

    // Redirect to promotions page with a 0.3-second delay
    echo "<script>
        setTimeout(function() {
            window.location.href = 'promotions.php';
        }, 300);
    </script>";
}

// Fetch the promotion details to edit
$promotion_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
$stmt->execute([$promotion_id]);
$promotion = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Promotion</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/sidebar.css">
<style>
    .main {
        padding: 20px;
        background-color: #f4f4f9;
        font-family: Arial, sans-serif;
    }
    h2 {
        color: #333;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .message {
        background-color: #e0f7fa;
        color: #00796b;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #00796b;
        border-radius: 5px;
    }
    .edit-promotion-form {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
    }
    .edit-promotion-form table {
        width: 100%;
    }
    .edit-promotion-form th {
        text-align: left;
        padding: 8px 0;
        color: #00796b;
        width: 35%;
    }
    .edit-promotion-form td {
        padding: 8px 0;
    }
    .edit-promotion-form input[type="text"],
    .edit-promotion-form input[type="number"],
    .edit-promotion-form input[type="date"],
    .edit-promotion-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .edit-promotion-form input[type="submit"] {
        background-color: #00796b;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }
    .edit-promotion-form input[type="submit"]:hover {
        background-color: #005c4b;
    }
</style>
</head>
<body>
<?php include '../components/sidebar.php'; ?>
<div class="main">
    <h2>Edit Promotion</h2>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?php foreach ($message as $msg): ?>
                <p><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Edit Promotion Form -->
    <div class="edit-promotion-form">
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $promotion['id']; ?>">
            <table>
                <tr>
                    <th>Promotion Name:</th>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($promotion['name']); ?>" required></td>
                </tr>
                <tr>
                    <th>Description:</th>
                    <td><textarea name="description" required><?= htmlspecialchars($promotion['description']); ?></textarea></td>
                </tr>
                <tr>
                    <th>Discount Percentage:</th>
                    <td><input type="number" name="discount_percentage" value="<?= htmlspecialchars($promotion['discount_percentage']); ?>" step="0.01" required></td>
                </tr>
                <tr>
                    <th>Start Date:</th>
                    <td><input type="date" name="start_date" value="<?= htmlspecialchars($promotion['start_date']); ?>" required></td>
                </tr>
                <tr>
                    <th>End Date:</th>
                    <td><input type="date" name="end_date" value="<?= htmlspecialchars($promotion['end_date']); ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="update_promotion" value="Update Promotion">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
</body>
</html>

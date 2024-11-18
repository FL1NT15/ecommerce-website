<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Check if the user is logged in
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit(); // Added exit to prevent further execution
}

// Fetch the role of the logged-in admin and store it in the session
$select_admin = $conn->prepare("SELECT role_id FROM admins WHERE id = ?");
$select_admin->execute([$admin_id]);
$fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);

// Check if a valid admin was fetched and set the role_id
if ($fetch_admin) {
    $role_id = $fetch_admin['role_id']; // Store role_id in the variable
    $_SESSION['role_id'] = $role_id; // Store role_id in session if needed
} else {
    // Handle case where admin ID is not found
    session_destroy();
    header('location:admin_login.php');
    exit();
}

// Check if a delete request was made
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
    $delete_admins->execute([$delete_id]);
    header('location:admin_accounts.php');
    exit(); // Added exit to prevent further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">
    <h1 class="heading">Admin Accounts</h1>
    <div class="box-container">

        <!-- Only Admins (role_id = 1) can add new admins -->
        <?php if (isset($role_id) && $role_id == 1): ?>
            <div class="box">
                <p>Add New Admin</p>
                <a href="register_admin.php" class="option-btn">Register Admin</a><br>
            </div>
        <?php endif; ?>

        <?php
        // Fetch admin accounts along with their roles
        $select_accounts = $conn->prepare("
            SELECT admins.*, roles.role_name FROM `admins`
            LEFT JOIN roles ON admins.role_id = roles.id
        ");
        $select_accounts->execute();
        
        if ($select_accounts->rowCount() > 0) {
            while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <p> Admin ID : <span><?= $fetch_accounts['id']; ?></span> </p>
                    <p> Admin Name : <span><?= $fetch_accounts['name']; ?></span> </p>
                    <p> Role : <span><?= $fetch_accounts['role_name']; ?></span> </p> <!-- Display role name -->
                    <div class="flex-btn">
                        <!-- Only Admins (role_id = 1) can delete accounts -->
                        <?php if (isset($role_id) && $role_id == 1): ?>
                            <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('Delete this account?')" class="delete-btn">Delete</a>
                        <?php endif; ?>

                        <!-- Button to update role -->
                        <?php if (isset($role_id) && $role_id == 1): ?>
                            <a href="update_profile.php?id=<?= $fetch_accounts['id']; ?>" class="option-btn">Update Account</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No accounts available!</p>';
        }
        ?>

    </div>
</section>

<script src="../js/admin_script.js"></script>

</body>
</html>

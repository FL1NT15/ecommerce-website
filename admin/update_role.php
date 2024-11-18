<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Check if the user is logged in
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit(); // Stop further execution after redirect
}

// Get the admin ID to update (from URL or session)
$admin_to_update = isset($_GET['id']) ? intval($_GET['id']) : $admin_id;

// Fetch the current profile details
$fetch_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$fetch_profile->execute([$admin_to_update]);
$current_profile = $fetch_profile->fetch(PDO::FETCH_ASSOC);

if (!$current_profile) {
    echo "Admin not found.";
    exit();
}

// Fetch all roles
$select_roles = $conn->prepare("SELECT * FROM roles");
$select_roles->execute();
$roles = $select_roles->fetchAll(PDO::FETCH_ASSOC);

// Initialize message array
$message = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update name and password logic
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);

    // Update name in the database
    $update_profile_name = $conn->prepare("UPDATE `admins` SET name = ? WHERE id = ?");
    $update_profile_name->execute([$name, $admin_to_update]);

    // Password management
    $old_pass = filter_var(trim($_POST['old_pass']), FILTER_SANITIZE_STRING);
    $new_pass = filter_var(trim($_POST['new_pass']), FILTER_SANITIZE_STRING);
    $confirm_pass = filter_var(trim($_POST['confirm_pass']), FILTER_SANITIZE_STRING);

    // Verify old password using password_verify
    if (!empty($old_pass)) {
        if (!password_verify($old_pass, $current_profile['password'])) {
            $message[] = 'Old password does not match!';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'Confirm password does not match!';
        } elseif (!empty($new_pass)) {
            // Hash and update the new password
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_admin_pass = $conn->prepare("UPDATE `admins` SET password = ? WHERE id = ?");
            $update_admin_pass->execute([$hashed_new_pass, $admin_to_update]);
            $message[] = 'Password updated successfully!';
        }
    }

    // Update role logic (if role_id is set)
    if (isset($_POST['role_id'])) {
        $new_role_id = $_POST['role_id'];
        $update_role = $conn->prepare("UPDATE admins SET role_id = ? WHERE id = ?");
        $update_role->execute([$new_role_id, $admin_to_update]);
        $message[] = 'Role updated successfully!';
    }

    header('location:admin_accounts.php'); // Redirect after update
    exit(); // Prevent further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile and Role</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Update Profile and Role</h3>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <?php foreach ($message as $msg): ?>
                <div class="message"><?= htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Update Name -->
        <input type="text" name="name" value="<?= htmlspecialchars($current_profile['name']); ?>" required placeholder="Enter your username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

        <!-- Update Password -->
        <input type="password" name="old_pass" placeholder="Enter old password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="new_pass" placeholder="Enter new password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="confirm_pass" placeholder="Confirm new password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

        <!-- Update Role -->
        <label for="role_id">Select New Role:</label>
        <select name="role_id" id="role_id" required>
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id']; ?>" <?= ($role['id'] == $current_profile['role_id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($role['role_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Submit Button -->
        <input type="submit" value="Update Now" class="btn">
    </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

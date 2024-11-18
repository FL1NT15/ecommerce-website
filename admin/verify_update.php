<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Check if the user is logged in
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Check if the ID is set in the GET request

if (isset($_GET['id'])) {
    $update_id = $_GET['id'];

    // Fetch the admin's current details to verify/update as needed
    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
    $select_admin->execute([$update_id]);
    $admin_details = $select_admin->fetch(PDO::FETCH_ASSOC);
    
    // Check if admin details exist
    if (!$admin_details) {
        echo "Admin not found!";
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}


// Verification logic
if (isset($_POST['verify'])) {
    $admin_name = filter_var($_POST['admin_name'], FILTER_SANITIZE_STRING);
    $admin_pass = filter_var(sha1($_POST['admin_pass']), FILTER_SANITIZE_STRING);

    // Check if the provided credentials match the database
    if ($admin_details['name'] === $admin_name && $admin_details['password'] === $admin_pass) {
        // Proceed to show the update form if credentials are valid
        header("Location: update_profile.php?id=$update_id"); // Redirect to the update profile page
        exit();
    } else {
        $message[] = 'Verification failed! Please check your username and password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Update</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Verify Identity</h3>
        <input type="text" name="admin_name" placeholder="Enter your username" required class="box">
        <input type="password" name="admin_pass" placeholder="Enter your password" required class="box">
        <input type="submit" value="Verify" class="btn" name="verify">
    </form>

    <?php
    // Display error messages if any
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<p class="error-msg">' . $msg . '</p>';
        }
    }
    ?>
</section>

<script src="../js/admin_script.js"></script>

</body>
</html>

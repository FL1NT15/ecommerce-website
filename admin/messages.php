<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Check if the user is logged in
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit(); // Stop further execution
}

// Fetch the role of the logged-in admin
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
    
    // Only allow deletion if the user has the correct role (Admin or Manager)
    if ($role_id == 1 || $role_id == 3) {
        $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
        $delete_message->execute([$delete_id]);
        header('location:messages.php'); // Redirect back to messages after deletion
        exit(); // Stop further execution
    } else {
        echo "You do not have permission to delete messages.";
        exit(); // Stop further execution
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">
<h1 class="heading">Messages</h1>

<div class="box-container">
   <?php
      $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      if($select_messages->rowCount() > 0){
         while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
       <p> User ID: <span><?= htmlspecialchars($fetch_message['user_id']); ?></span></p>
       <p> Name: <span><?= htmlspecialchars($fetch_message['name']); ?></span></p>
       <p> Email: <span><?= htmlspecialchars($fetch_message['email']); ?></span></p>
       <p> Number: <span><?= htmlspecialchars($fetch_message['number']); ?></span></p>
       <p> Message: <span><?= htmlspecialchars($fetch_message['message']); ?></span></p>
       
       <!-- Show delete button only for Admin and Manager -->
       <?php if ($role_id == 1 || $role_id == 3): ?>
           <a href="messages.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('Delete this message?');" class="delete-btn">Delete</a>
       <?php endif; ?>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">You have no messages</p>';
      }
   ?>
</div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>

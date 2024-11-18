<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

// Handle the return or refund request submission
if(isset($_POST['request_type']) && isset($_POST['order_id'])){
   $order_id = $_POST['order_id'];
   $request_type = $_POST['request_type'];

   // Update the payment status based on the request type
   if($request_type == 'return'){
      $new_status = 'Return Request';
   } elseif($request_type == 'refund'){
      $new_status = 'Refund Request';
   }

   // Update the order status
   $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_order->execute([$new_status, $order_id]);

   // Redirect to prevent form resubmission
   header('Location: orders.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="shortcut icon" type="x-icon" href="Logos/LogoTab.png">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders GoCart</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <script>
      function showAlert() {
         alert("Your request has been successfully sent. Please wait for the processing of your request.");
      }
   </script>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">placed orders</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">please login to see your orders</p>';
      }else{
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>placed on : <span><?= $fetch_orders['placed_on']; ?></span></p>
      <p>name : <span><?= $fetch_orders['name']; ?></span></p>
      <p>email : <span><?= $fetch_orders['email']; ?></span></p>
      <p>number : <span><?= $fetch_orders['number']; ?></span></p>
      <p>address : <span><?= $fetch_orders['address']; ?></span></p>
      <p>payment method : <span><?= $fetch_orders['method']; ?></span></p>
      <p>your orders : <span><?= $fetch_orders['total_products']; ?></span></p>
      <p>total price : <span>₱<?= $fetch_orders['total_price']; ?>/-</span></p>
      <p> payment status : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; } else if($fetch_orders['payment_status'] == 'Return Request' || $fetch_orders['payment_status'] == 'Refund Request'){ echo 'orange'; } else { echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>

      <!-- Return and Refund Buttons -->
      <?php if($fetch_orders['payment_status'] == 'pending'): ?>
         <form action="orders.php" method="POST" onsubmit="showAlert()">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <button type="submit" name="request_type" value="return" class="btn btn-warning">Return Request</button>
         </form>
         <form action="orders.php" method="POST" onsubmit="showAlert()">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <button type="submit" name="request_type" value="refund" class="btn btn-danger">Refund Request</button>
         </form>
      <?php endif; ?>

   </div>
   <?php
      }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

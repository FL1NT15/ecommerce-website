<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:user_login.php');
}

if (isset($_POST['order'])) {

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
    $address = 'flat no. ' . filter_var($_POST['flat'], FILTER_SANITIZE_STRING) . ', ' . filter_var($_POST['street'], FILTER_SANITIZE_STRING) . ', ' . filter_var($_POST['city'], FILTER_SANITIZE_STRING) . ', ' . filter_var($_POST['state'], FILTER_SANITIZE_STRING) . ', ' . filter_var($_POST['country'], FILTER_SANITIZE_STRING) . ' - ' . filter_var($_POST['pin_code'], FILTER_SANITIZE_STRING);
    $total_products = $_POST['total_products'];
    $total_price = $_POST['total_price'];

    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $check_cart->execute([$user_id]);

    if ($check_cart->rowCount() > 0) {
        // Insert the order into the orders table
        $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
        $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

        // Update stock for each product
        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $select_cart->execute([$user_id]);

        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            $pid = $fetch_cart['pid']; // Assuming your cart table has a product_id field
            $quantity = $fetch_cart['quantity'];

            // Update the stock in the products table
            $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
            $update_stock->execute([$quantity, $pid]);
        }

        // Clear the cart
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        $message[] = 'Order placed successfully!';
    } else {
        $message[] = 'Your cart is empty';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="shortcut icon" type="x-icon" href="Logos/LogoTab.png">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
<style> 
   /* Styles for the Checkout Orders Section */


/* Table Styles */
.table {
   width: 100%;
   border-collapse: collapse;
   margin-bottom: 20px;
}

.table thead th {
   background-color: #333;
   color: #fff;
   font-weight: bold;
   padding: 10px;
   text-align: left;
}

.table tbody td {
   padding: 10px;
   border: 1px solid #ddd;
   text-align: left;
}

/* Image Styling in Table */
.table tbody td img {
   width: 50px;
   height: 50px;
   object-fit: cover;
   border-radius: 4px;
}

/* Total Products and Price Row Styling */
.table tbody tr:last-child td {
   font-weight: bold;
   background-color: #f0f0f0;
   text-align: right;
}

/* Centering Total Products and Total Price Row */
.table tbody tr:nth-last-child(2) td,
.table tbody tr:last-child td {
   text-align: right;
   color: #333;
}

/* Styling Grand Total Value */
.table tbody tr:last-child td span {
   color: #d9534f; /* Red color for emphasis */
   font-weight: bold;
}



</style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">
   <form action="" method="POST">
      <h3>Your Orders</h3>
      
      <?php
      $grand_total = 0; 
      $cart_items = [];
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if($select_cart->rowCount() > 0){
      ?>
      <table class="table table-bordered">
         <thead>
            <tr>
               <th>Product Name</th>
               <th>Quantity</th>
               <th>Price</th>
               <th>Total Price</th>
            </tr>
         </thead>
         <tbody>
         <?php
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $product_total = $fetch_cart['price'] * $fetch_cart['quantity'];
            $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].')';
            $grand_total += $product_total;
         ?>
            <tr>
   
               <td><?= htmlspecialchars($fetch_cart['name']); ?></td>
               <td><?= htmlspecialchars($fetch_cart['quantity']); ?></td>
               <td>₱<?= htmlspecialchars($fetch_cart['price']); ?></td>
               <td>₱<?= htmlspecialchars($product_total); ?></td>
            </tr>
         <?php
         }
         ?>
         <!-- Row for Total Products and Grand Total -->
         <tr>
            <td colspan="3" style="text-align: right;"><strong>Total Products:</strong></td>
            <td colspan="2"><?= count($cart_items); ?></td>
         </tr>
         <tr>
            <td colspan="3" style="text-align: right;"><strong>Sub Total Price:</strong></td>
            <td colspan="2"><span>₱<?= $grand_total; ?>/-</span></td>
         </tr>
         </tbody>
      </table>

      <?php
      } else {
         echo '<p class="empty">Your cart is empty!</p>';
      }
      ?>

      <!-- Hidden Inputs to Pass Total Products and Price -->
      <input type="hidden" name="total_products" value="<?= implode(', ', $cart_items); ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">




      <h3>place your orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" placeholder="enter your number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" placeholder="e.g. mumbai" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>state :</span>
            <input type="text" name="state" placeholder="e.g. maharashtra" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>country :</span>
            <input type="text" name="country" placeholder="e.g. India" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" class="box" required onkeypress="if(this.value.length == 6) return false;">
         </div>
      </div>

      <!-- Embed Google Maps iframe -->
      <h3>Your Location</h3>
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15443.77613174292!2d121.02905510421553!3d14.602264073204871!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c829f85e1571%3A0xe9ed7073390ef434!2sSan%20Juan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1728483675637!5m2!1sen!2sph" width="1115" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>



<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
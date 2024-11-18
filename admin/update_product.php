<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update'])){

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   
   $stock = $_POST['stock']; // Get the stock value
   $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT); // Ensure it's an integer

   // Get the category from the form
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);

   // Update the product including category and stock
   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ?, stock = ?, category = ? WHERE id = ?");
   $update_product->execute([$name, $price, $details, $stock, $category, $pid]);

   $message[] = 'Product updated successfully!';

   // Handle image update logic...
   // (rest of the image update code remains the same)
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">
   <h1 class="heading">update product</h1>

   <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
 <form action="" method="post" enctype="multipart/form-data">
   <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
   <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
   <input type="hidden" name="old_image_02" value="<?= $fetch_products['image_02']; ?>">
   <input type="hidden" name="old_image_03" value="<?= $fetch_products['image_03']; ?>">

   <div class="image-container">
      <div class="main-image">
         <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
      </div>
      <div class="sub-image">
         <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         <img src="../uploaded_img/<?= $fetch_products['image_02']; ?>" alt="">
         <img src="../uploaded_img/<?= $fetch_products['image_03']; ?>" alt="">
      </div>
   </div>

   <span>update name</span>
   <input type="text" name="name" required class="box" maxlength="100" placeholder="enter product name" value="<?= $fetch_products['name']; ?>">
   
   <span>update price</span>
   <input type="number" name="price" required class="box" min="0" max="9999999999" placeholder="enter product price" onkeypress="if(this.value.length == 10) return false;" value="<?= $fetch_products['price']; ?>">

   <!-- Stock input moved under price -->
   <span>update stock</span>
   <input type="number" name="stock" required class="box" min="0" max="9999999999" placeholder="enter product stock" value="<?= $fetch_products['stock']; ?>">

 <!-- Category dropdown -->
 <span>update category</span>
   <select name="category" class="box" required>
      <option value="pork" <?= $fetch_products['category'] == 'pork' ? 'selected' : '' ?>>Pork</option>
      <option value="chicken" <?= $fetch_products['category'] == 'chicken' ? 'selected' : '' ?>>Chicken</option>
      <option value="beef" <?= $fetch_products['category'] == 'beef' ? 'selected' : '' ?>>Beef</option>
      <option value="fish" <?= $fetch_products['category'] == 'fish' ? 'selected' : '' ?>>Fish</option>
      <option value="marinated" <?= $fetch_products['category'] == 'marinated' ? 'selected' : '' ?>>Marinated Food</option>
   </select>


   <span>update details</span>
   <textarea name="details" class="box" required cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>

  
   <span>update image 01</span>
   <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
   <span>update image 02</span>
   <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
   <span>update image 03</span>
   <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
   <div class="flex-btn">
      <input type="submit" name="update" class="btn" value="update">
      <a href="products.php" class="option-btn">go back</a>
   </div>
</form>

   <?php
         }
      }else{
         echo '<p class="empty">no product found!</p>';
      }
   ?>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="shortcut icon" type="x-icon" href="Logos/Carnivore'sCorner.png">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Carnivore's Corner</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">

   <div class="swiper home-slider">
   
   <div class="swiper-wrapper">

   <div class="swiper-slide slide">

         <div class="image">
            <img src="Logos/pork_cut.png" alt="">
         </div>
         <div class="content">
            <span>Pork Cuts</span>
            <h3>Satisfy your Meat Cravings</h3>
            <a href="shop.php" class="btn">shop now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="Logos/beef_cut.png" alt="">
         </div>
         <div class="content">
            <span>Beef Cuts</span>
            <h3>Satisfy your Meat Cravings</h3>
            <a href="shop.php" class="btn">shop now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="Logos/chicken_cut.jpg" alt="">
         </div>
         <div class="content">
            <span>Chicken Cuts</span>
            <h3>Satisfy your Meat Cravings</h3>
            <a href="shop.php" class="btn">shop now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="Logos/fish_cut.png" alt="">
         </div>
         <div class="content">
            <span>Fish Cuts</span>
            <h3>Satisfy your Meat Cravings</h3>
            <a href="shop.php" class="btn">shop now</a>
         </div>
      </div>

      <div class="swiper-slide slide">
         <div class="image">
            <img src="Logos/marinated.png" alt="">
         </div>
         <div class="content">
            <span>Marinated</span>
            <h3>Satisfy your Meat Cravings</h3>
            <a href="shop.php" class="btn">shop now</a>
         </div>
      </div>

   </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

</div>

<!-- ======================================= -->

<section class="category">

   <h1 class="heading">SHOP</h1>

   <div class="swiper category-slider">

   <div class="swiper-wrapper">

   <a href="category.php?category=pork" class="swiper-slide slide"> 
      <img src="Logos/pork_cut.png" alt="">
      <h3>Pork Cuts</h3>
   </a>

   <a href="category.php?category=beef" class="swiper-slide slide">
      <img src="Logos/beef_cut.png" alt="">
      <h3>Beef Cuts</h3>
   </a>

   <a href="category.php?category=chicken" class="swiper-slide slide">
      <img src="Logos/chicken_cut.jpg" alt="">
      <h3>Chicken Cuts</h3>
   </a>

   <a href="category.php?category=fish" class="swiper-slide slide">
      <img src="Logos/fish_cut.png" alt="">
      <h3>Fish Cuts</h3>
   </a>

   <a href="category.php?category=marinated" class="swiper-slide slide">
      <img src="Logos/marinated.png" alt="">
      <h3>Marinated</h3>
   </a>
   

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>

<!-- ======================================= -->

<section class="home-products">

   <h1 class="heading">latest products</h1>

   <div class="swiper products-slider">

   <div class="swiper-wrapper">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
  <form action="" method="post" class="swiper-slide slide">
    <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
    <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
    <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
    <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
    <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
    <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
    <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
    <div class="name"><?= $fetch_product['name']; ?></div>
    <div class="flex">
        <div class="price"><span>₱</span><?= $fetch_product['price']; ?><span>/-</span></div>
        <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
    </div>
    <div class="stock" style="font-size: 1.2em; color: #555;">Stock: <?= $fetch_product['stock']; ?></div>
    
    <!-- Add to Cart button with stock check -->
    <?php if ($fetch_product['stock'] > 0): ?>
        <input type="submit" value="add to cart" class="btn" name="add_to_cart">
    <?php else: ?>
        <input type="button" value="Out of Stock" class="btn disabled" style="background-color: gray;" onclick="alert('This product is out of stock');">
    <?php endif; ?>
</form>

   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>


<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
    },
});

 var swiper = new Swiper(".category-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

var swiper = new Swiper(".products-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      550: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
   },
});

</script>

<script>
// JavaScript code here
window.addEventListener('scroll', function() {
   var slideElements = document.querySelectorAll('.category .slide');
   var scrollPosition = window.scrollY;

   slideElements.forEach(function(slide) {
      if (scrollPosition > 100) { // Adjust the scroll position threshold as needed
         slide.classList.add('scrolled');
      } else {
         slide.classList.remove('scrolled');
      }
   });
});

</script>

</body>
</html>
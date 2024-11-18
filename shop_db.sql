-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 07:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `password`, `role_id`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 1),
(3, 'hotsoles', '356a192b7913b04c54574d18c28d46e6395428ab', 2),
(4, 'PerfumeScapes', '6efbc08e212f73a56fb597b4d39e2f10f9917499', 2),
(5, 'JoshuaMeatProducts', '4d45bdc7feaad06b97c8fe3433aaae1b2215c14f', 3),
(6, 'test', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 2),
(7, 'test1', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 3),
(8, 'test2', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', NULL),
(9, 'test12', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 2);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(1, 0, 'tester1', 'tester@gmail.com', '0992009923', 'tester item'),
(2, 2, 'tester2', 'tester2@gmail.com', '0995202192', 'tester2 item');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(37, 34, 'test', '111', 'katefatma84@gmail.com', 'cash on delivery', 'flat no. 111, 423, mumbai, eyy, aww - 1413', 'Center cut Pork chops (150 x 5) - ', 750, '2024-10-07', 'delivered'),
(38, 34, 'test', '1441', 'flinttop23@gmail.com', 'cash on delivery', 'flat no. 111, 423, mumbai, eyy, aww - 14123', 'Center cut Pork chops (150 x 1) - ', 150, '2024-10-08', 'delivered'),
(39, 34, 'test', '14141', '111@gmail.com', 'cash on delivery', 'flat no. 111, 423, mumbai, eyy, aww - 411', ' Butterfly Fish (140 x 8) - Beef CHUCK (140 x 6) - Marinated BALSAMIC PORK CHOP (230 x 7) - ', 3570, '2024-10-11', 'delivered'),
(40, 34, 'test1', '14141', '111@gmail.com', 'cash on delivery', 'flat no. 114, 111, mumbai, eyy, aww - 441', 'Chicken 3 Joint (110 x 4) - ', 440, '2024-10-11', 'delivered');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(10) NOT NULL,
  `image_01` varchar(100) NOT NULL,
  `image_02` varchar(100) NOT NULL,
  `image_03` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `details`, `price`, `image_01`, `image_02`, `image_03`, `stock`, `category`, `created_at`) VALUES
(49, 'Center cut Pork chops', 'Center cut pork chops are thick, juicy cuts from the pork loin, featuring a balance of meat and a small amount of fat. Known for their tenderness and rich flavor, they are versatile for grilling, baking, or pan-searing. Ideal for a delicious and satisfying meal.\r\n', 150, 'Center cut pork chops.jpg', 'Center cut pork chops.jpg', 'Center cut pork chops.jpg', 94, 'pork', '2024-10-07 17:08:41'),
(51, 'Pork Rib Roast', 'Rib roast, or pork rib roast, is a succulent cut from the rib section of the pig, often featuring a flavorful layer of fat. Known for its tenderness and rich taste, it is typically roasted to perfection, making it ideal for special occasions and family gatherings. This cut is perfect for serving as an impressive centerpiece.', 170, 'Rib Roast.jpg', 'Rib Roast.jpg', 'Rib Roast.jpg', 100, 'pork', '2024-10-07 17:08:41'),
(52, 'Beef BRISKET', 'Beef brisket is a flavorful cut from the chest of the cow, known for its tenderness when cooked low and slow. It has a rich, beefy taste and is commonly used for smoking, braising, or roasting. Ideal for hearty dishes like barbecue or pot roast.\r\n', 130, 'BRISKET.jpg', 'BRISKET.jpg', 'BRISKET.jpg', 100, 'beef', '2024-10-07 17:08:41'),
(53, 'Beef CHUCK', 'Chuck beef is a cut from the shoulder of the cow, known for its rich flavor and marbling. It is versatile and often used for braising, stews, or ground beef. Ideal for slow cooking, it becomes tender and juicy, making it a favorite for hearty dishes.', 140, 'CHUCK.jpg', 'CHUCK.jpg', 'CHUCK.jpg', 94, 'beef', '2024-10-07 17:08:41'),
(54, 'Beef RIB', 'Rib beef comes from the rib section of the cow, known for its tenderness and rich flavor. Popular cuts include ribeye and prime rib. Ideal for grilling, roasting, or braising, rib beef is prized for its marbling, which adds juiciness and depth to dishes.', 150, 'RIB.jpg', 'RIB.jpg', 'RIB.jpg', 0, 'beef', '2024-10-07 17:08:41'),
(55, 'Beef Shank', 'Beef shank is a cut from the leg of the cow, known for its rich flavor and tough texture. It is often used for slow cooking, braising, or in soups and stews. The shank becomes tender and flavorful over long cooking times, making it ideal for hearty dishes.', 160, 'SHANK.jpg', 'SHANK.jpg', 'SHANK.jpg', 0, 'beef', '2024-10-07 17:08:41'),
(56, 'Beef SHORT PLATE', 'Short plate is a cut from the lower rib area of the cow, known for its flavorful and fatty meat. It includes popular cuts like skirt steak and short ribs. Ideal for grilling or braising, short plate is often used in tacos, stir-fries, and barbecue dishes.\r\n', 170, 'SHORT PLATE.jpg', 'SHORT PLATE.jpg', 'SHORT PLATE.jpg', 0, 'beef', '2024-10-07 17:08:41'),
(57, 'Beef SIRLOIN', 'Sirloin is a cut from the rear of the cow, known for its balance of tenderness and flavor. It includes cuts like top sirloin and sirloin steak. Versatile and relatively lean, sirloin is great for grilling, roasting, or pan-searing, making it a popular choice for steaks and stir-fries.', 180, 'SIRLOIN.jpg', 'SIRLOIN.jpg', 'SIRLOIN.jpg', 0, 'beef', '2024-10-07 17:08:41'),
(58, 'Chicken 3 Joint', 'The 3 joint chicken cut refers to the leg section, which includes the drumstick, thigh, and the joint connecting them. This cut is flavorful and juicy, making it perfect for roasting, grilling, or braising. It’s a popular choice for hearty dishes and comfort food.', 110, '3joint.png', '3joint.png', '3joint.png', 51, 'chicken', '2024-10-07 17:08:41'),
(59, 'Chicken Breast', 'Chicken breast is a lean, boneless cut from the front of the bird. Known for its mild flavor and versatility, it can be grilled, baked, sautéed, or used in various dishes. Ideal for healthy meals, it cooks quickly and absorbs marinades well. ', 135, 'Breast.png', 'Breast.png', 'Breast.png', 52, 'chicken', '2024-10-07 17:08:41'),
(60, 'Chicken Leg Quarter', 'The leg quarter chicken cut includes the thigh and drumstick, attached at the joint. This cut is flavorful and juicy, making it perfect for roasting, grilling, or frying. It’s a popular choice for hearty meals and offers a satisfying texture.', 155, 'Leg_Quarter.png', 'Leg_Quarter.png', 'Leg_Quarter.png', 0, 'chicken', '2024-10-07 17:08:41'),
(61, 'Chicken THIGH', 'Chicken thigh is a tender, flavorful cut from the upper leg of the bird. Known for its juiciness and rich taste, it can be cooked bone-in or boneless, making it ideal for grilling, baking, or stewing. It&#39;s a favorite for hearty dishes and comfort food.\r\n', 145, 'thigh.png', 'thigh.png', 'thigh.png', 0, 'chicken', '2024-10-07 17:08:41'),
(62, 'Whole Chicken', 'A whole chicken is the entire bird, including all parts breasts, thighs, legs, and wings. It offers versatility for various cooking methods like roasting, grilling, or slow-cooking. Ideal for family meals, it allows for flavorful dishes and leftovers.', 250, 'Whole_Chicken.png', 'Whole_Chicken.png', 'Whole_Chicken.png', 0, 'chicken', '2024-10-07 17:08:41'),
(63, 'Chicken Wings', 'Chicken wings are small, flavorful cuts from the bird&#39;s wing section, typically divided into three parts: drumette, flat, and tip. Popular for frying, baking, or grilling, they are often served with sauces and are a favorite for snacks and parties.', 130, 'Wings.png', 'Wings.png', 'Wings.png', 0, 'chicken', '2024-10-07 17:08:41'),
(64, ' Butterfly Fish', 'The butterfly fish cut involves splitting the fish open from the back, removing the backbone, and flattening it out. This technique allows for even cooking and quick grilling or baking. It&#39;s ideal for marinades and presentation, showcasing the fish&#39;s delicate texture and flavor.', 140, 'Butterfly.png', 'Butterfly.png', 'Butterfly.png', 34, 'fish', '2024-10-07 17:08:41'),
(65, 'Fish Fillet', 'A fish fillet is a boneless cut of fish, typically taken from the sides of the fish. It is known for its tender texture and mild flavor, making it versatile for various cooking methods like grilling, baking, or pan-searing. Fish fillets are easy to prepare and cook quickly, perfect for weeknight meals.', 160, 'Fillet.png', 'Fillet.png', 'Fillet.png', 51, 'fish', '2024-10-07 17:08:41'),
(66, 'Goujon Fish', 'Goujon fish cut refers to thin strips of fish, typically around 1 inch wide, that are often breaded and fried. This cut is popular for creating crispy, bite-sized pieces, making it ideal for snacks, appetizers, or as a topping for salads and dishes.\r\n', 150, 'goujon.png', 'goujon.png', 'goujon.png', 0, 'fish', '2024-10-07 17:08:41'),
(67, 'Supreme Fish', 'The supreme fish cut is a boneless, skinless section of fish, often taken from the fillet and cut into elegant, portion-sized pieces. This cut is typically used for refined dishes, showcasing the fish&#39;s delicate texture and flavor, and is ideal for poaching, baking, or sautéing.', 200, 'Supreme.png', 'Supreme.png', 'Supreme.png', 0, 'fish', '2024-10-07 17:08:41'),
(68, 'Tail Fish ', 'The tail fish cut includes the tail section of the fish, often characterized by its tapered shape. This cut is flavorful and can be used in various cooking methods, such as grilling or baking. It&#39;s commonly used for dishes that highlight the fish&#39;s natural taste and texture.', 120, 'Tail.png', 'Tail.png', 'Tail.png', 0, 'fish', '2024-10-07 17:08:41'),
(69, 'Troncon Fish', 'The troncon fish cut is a thick, cross-sectional slice taken from the fish, typically from the center or body. This cut includes the skin and is ideal for grilling or roasting, showcasing the fish&#39;s rich flavor and meaty texture.\r\n', 134, 'troncon.png', 'troncon.png', 'troncon.png', 0, 'fish', '2024-10-07 17:08:41'),
(70, 'Marinated BALSAMIC PORK CHOP', 'Balsamic pork chop is a flavorful dish featuring pork chops marinated in balsamic vinegar, garlic, and herbs. The marinade enhances the meat&#39;s natural sweetness and tenderness. Typically grilled or pan-seared, it offers a delicious combination of savory and tangy flavors, perfect for a quick and satisfying meal.', 230, 'Balsamic Porkchop.jpg', 'Balsamic Porkchop.jpg', 'Balsamic Porkchop.jpg', 24, 'marinated', '2024-10-07 17:08:41'),
(71, 'Marinated BEEF HONEY MUSTARD', 'Beef honey mustard is a dish featuring tender beef marinated or glazed with a mixture of honey and mustard. The sweet and tangy flavors complement the savory beef, making it ideal for grilling or roasting. This dish offers a deliciously balanced taste, perfect for a quick and flavorful meal.', 240, 'Beef Honey Mustard.jpg', 'Beef Honey Mustard.jpg', 'Beef Honey Mustard.jpg', 42, 'marinated', '2024-10-07 17:08:41'),
(72, 'Marinated CHICKEN BBQ', 'Chicken BBQ refers to chicken pieces marinated or coated in barbecue sauce and grilled or smoked to perfection. The result is tender, juicy meat with a smoky, sweet, and tangy flavor. It&#39;s a popular dish for cookouts and gatherings.', 255, 'Chicken BBQ.jpg', 'Chicken BBQ.jpg', 'Chicken BBQ.jpg', 0, 'marinated', '2024-10-07 17:08:41'),
(73, 'Marinated Chipotle Chicken ', 'Chipotle chicken features chicken marinated in a blend of chipotle peppers, spices, and often lime juice, resulting in a smoky, spicy flavor. It can be grilled, baked, or sautéed, making it versatile for tacos, salads, or bowls. Perfect for those who enjoy a bit of heat in their meals.', 260, 'Chipotle Chicken.jpg', 'Chipotle Chicken.jpg', 'Chipotle Chicken.jpg', 0, 'marinated', '2024-10-07 17:08:41'),
(74, 'Marinated Fish Lemon Garlic', 'Fish lemon garlic is a dish featuring fish fillets seasoned with fresh lemon juice, garlic, and herbs. The combination creates a bright, zesty flavor that enhances the fish&#39;s natural taste. Typically baked or pan-seared, it&#39;s a light and healthy option perfect for any meal.', 235, 'Fish Lemon garlic.jpg', 'Fish Lemon garlic.jpg', 'Fish Lemon garlic.jpg', 0, 'marinated', '2024-10-07 17:08:41'),
(75, 'Marinated Pork Cilantro', 'Pork cilantro is a dish featuring tender pork marinated with fresh cilantro, garlic, and spices. The vibrant herb adds a refreshing flavor, making it ideal for grilling, sautéing, or slow cooking. Perfect for tacos, rice bowls, or as a flavorful main course.', 270, 'Pork Cilantro.jpg', 'Pork Cilantro.jpg', 'Pork Cilantro.jpg', 0, 'marinated', '2024-10-07 17:08:41'),
(84, 'Boneless Pork Loin', 'Boneless pork loin is a lean and tender cut from the back of the pig, free of bones for easy preparation. It has a mild flavor and can be roasted, grilled, or sautéed. Versatile and quick to cook, it works well with a variety of seasonings and is perfect for weeknight meals or special occasions.', 250, 'Boneless Pork loin.jpg', 'Boneless Pork loin.jpg', 'Boneless Pork loin.jpg', 100, 'pork', '2024-10-07 17:08:41'),
(86, 'Pork Belly', 'Pork belly is a rich, fatty cut from the underside of the pig, known for its layers of meat and fat. It has a savory flavor and is often used in dishes like braised pork belly, bacon, or crispy pork belly. This versatile cut can be roasted, grilled, or slow-cooked for delicious results.', 350, 'Pork Belly.jpg', 'Pork Belly.jpg', 'Pork Belly.jpg', 100, 'pork', '2024-10-07 17:08:41'),
(95, 'B-Loin', 'Pork loin is a lean and versatile cut taken from the back of the pig. It is known for its mild flavor and tenderness. Commonly available as roasts, chops, or steaks, pork loin is ideal for roasting, grilling, or sautéing. It pairs well with various seasonings and sides, making it a popular choice for many dishes.', 145, 'B-Loin.png', 'B-Loin.png', 'B-Loin.png', 44, 'pork', '2024-10-10 16:38:31'),
(96, 'Pork Baby back ribs', 'Tender and flavorful, pork baby back ribs are cut from the back of the pig. Known for their meaty texture and slight sweetness, they are ideal for barbecuing and grilling, often served with BBQ sauce and classic sides. Perfect for gatherings and cookouts!', 555, 'Baby back ribs.jpg', 'Baby back ribs.jpg', 'Baby back ribs.jpg', 41, 'pork', '2024-10-10 16:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Sales'),
(3, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'tester', 'tester@gmail.com', '4d7547a1d2787c72f0e985d8b5194295e4aa6141'),
(2, 'tester2', 'tester2@gmail.com', '5e86e8cdc7188d53916fcd1d7294cee4611c7c49'),
(3, 'Maria T. Santos', 'mariatsantos7362@gmail.com', 'c6f9730a463e24f72ac96b80babe582c7d52bdf4'),
(4, 'Juan M. dela Cruz', 'juanmdelacruz5487@gmail.com', 'd8721944283ac23a3deab29f7f9e7667e99547a7'),
(5, 'Andrea G. Garcia', 'andreaggarcia1923@gmail.com', '9a329a3c681ee6bdb1a64c5862e490f7f94f95c5'),
(6, 'Jose R. Reyes', 'joserrreyes3056@gmail.com', 'e72e66e3f0829350e9b9c8d0a89e44abc33c4d4b'),
(7, 'Ana L. Rodriguez', 'analrodriguez9074@gmail.com', 'fa537a162973796d5cbbee3cc1b0fbae602946a9'),
(8, 'Carlo P. Gonzales', 'carlopgonzales6318@gmail.com', '0ac6b5915975ae44e35e3931e9f99698e3360690'),
(9, 'Sofia S. Ramirez', 'sofiasramirez4289@gmail.com', '406307413ddb88695f70c9f3dacdbc2bdb94591a'),
(10, 'Miguel A. Cruz', 'miguelacruz2197@gmail.com', '332f1cceef93a21be2f5fda0ddc0d29bd541de34'),
(11, 'Angelica F. Fernande', 'angelicaffernandez5743@gmail.com', '5ded009789ac368d5ff14963e349a8274c1a6598'),
(12, 'Rafael V. Villanueva', 'rafaelvvillanueva8402@gmail.com', 'fe9a22ba63f1329ec37a373dc662681fb14c9746'),
(13, 'Patricia C. Martinez', 'patriciacmartinez1736@gmail.com', 'e03c81b21af94d694f8dca1048825cfde298f7d2'),
(14, 'Diego J. Torres', 'diegojtorres6082@gmail.com', 'c732e375a6dbea33cf1741083053348078008d84'),
(15, 'Gabriela R. Reyes', 'gabrielarreyes9587@gmail.com', 'fde020ff49266feb63e31286b09e34b70243c0d1'),
(16, 'Luisa D. Del Rosario', 'luisaddelrosario3014@gmail.com', '0c3614f2d10ae9e79a38c4efbdf455b5101b07d3'),
(17, 'Eduardo S. Rivera', 'eduardosrivera5096@gmail.com', '072d687c5d62843cec1a5a640f66877ea49f5c48'),
(18, 'Teresa O. Ocampo', 'teresaoocampo7429@gmail.com', '731d498e91a2687fbc341c354490ea7c9059e759'),
(19, 'Ramon V. Santos', 'ramonvsantos2165@gmail.com', 'b312bc15c1e0809e43ba978ccae9012ea1b95dbf'),
(20, 'Maricel L. Fernandez', 'maricellfernandez8041@gmail.com', 'b06ecb1ae9c2ac7bf373d2768a1930158d3001b0'),
(21, 'Fernando G. Lopez', 'fernandoglopez6973@gmail.com', '9cb5c11bb02c08d57470da3f5ac94d5c0dc236f8'),
(22, 'Lorna M. Cruz', 'lornamcruz3810@gmail.com', '9d832ddf3ed36157c6af8c547f8065c9849ca9b2'),
(23, 'Gabriel D. dela Rosa', 'gabrielddelarosa9246@gmail.com', 'c31c6a285381e2f0f7f5b8733c0934a55edb4d06'),
(24, 'Beatriz T. Gonzales', 'beatriztgonzales5189@gmail.com', '60ab38426b8aed44474346540c3c6708d1218eb8'),
(25, 'Daniel S. Manalo', 'danielsmanalo3654@gmail.com', '3fb3bcc8375ee6b5f5895a089364dff24be03cba'),
(26, 'Veronica N. Santos', 'veronicanosantos8103@gmail.com', '6e9ff48278340f75e284889ab97c7056cfad380b'),
(27, 'Lorenzo A. Cruz', 'lorenzoacruz6327@gmail.com', '225fb59835c2769f541659e2012e5e54748d0abb'),
(28, 'Isabella C. Reyes', 'isabellacreyes4701@gmail.com', '91887d019492d399ae70aa47facc0dcf37c913bc'),
(29, 'Emilio L. Ramirez', 'emiliolramirez2958@gmail.com', '1e4ae447c5dca61c3a36ea22b7198162cc08dccf'),
(30, 'Cecilia P. Rodriguez', 'ceciliaprodriguez1865@gmail.com', '60d27a8fc621e80139906573ac237e524c77b9e1'),
(31, 'Antonio T. Garcia', 'antoniotgarcia7394@gmail.com', 'c18899a0d407533ac36e2265b889d43d1d849782'),
(32, 'Julia R. Ramos', 'juliarramos5072@gmail.com', '53b4c63e1afb9a4efbd6b84623393c03eb90c965'),
(33, 'sampleuser', 'sampleuser@gmail.com', 'c37bca4afb8ff7f52f450b04c1973f37dfde48db'),
(34, 'test', '111@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php
// Connect to the database
include '../components/connect.php';

// Start session and check admin authentication
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

// Messages array for feedback
$message = [];

// Handle adding a new product
if (isset($_POST['add_product'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
    $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    // Image handling (simplified to one image)
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_folder = "../uploaded_img/$image";

    // Save product in database
    if (move_uploaded_file($image_tmp, $image_folder)) {
        $insert_product = $conn->prepare("INSERT INTO products (name, price, details, stock, category, image_01) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_product->execute([$name, $price, $details, $stock, $category, $image]);
        $message[] = 'Product added successfully!';
    } else {
        $message[] = 'Failed to upload image!';
    }
}


// Handle category addition
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);

    // Check if the category name is not empty
    if (!empty($category_name)) {
        try {
            // Check if the category already exists
            $checkCategory = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
            $checkCategory->bindParam(':name', $category_name, PDO::PARAM_STR);
            $checkCategory->execute();
            $categoryExists = $checkCategory->fetchColumn();

            if ($categoryExists > 0) {
                // Category already exists, show an error message
                $message[] = 'This category name already exists. Please choose a different name.';
            } else {
                // Insert the new category
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
                $stmt->bindParam(':name', $category_name, PDO::PARAM_STR);
                $stmt->execute();
                $message[] = 'Category added successfully.';

                // Redirect to the products page after adding a category
                header('Location: products.php'); // Redirect to the products page
                exit();
            }
        } catch (PDOException $e) {
            $message[] = 'Error: ' . $e->getMessage();
        }
    } else {
        $message[] = 'Category name cannot be empty.';
    }
}








// Handle category deletion
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    // Delete category from the database
    $delete_category = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $delete_category->execute([$category_id]);

    // Redirect to products page after deletion
    header('Location: products.php'); // This will go back to the product page
    exit();
}


if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];

    try {
        // Archive the product (instead of deleting)
        $archive_product = $conn->prepare("UPDATE products SET archived = 1 WHERE id = ?");
        $archive_product->execute([$product_id]);
        $message[] = "Product archived successfully!";
    } catch (PDOException $e) {
        $message[] = "Error archiving product: " . $e->getMessage();
    }

    header('Location: products.php');
    exit();
}

if (isset($_GET['restore_product'])) {
    $product_id = $_GET['restore_product'];

    try {
        // Restore the product by setting archived = 0
        $restore_product = $conn->prepare("UPDATE products SET archived = 0 WHERE id = ?");
        $restore_product->execute([$product_id]);
        $message[] = "Product restored successfully!";
    } catch (PDOException $e) {
        $message[] = "Error restoring product: " . $e->getMessage();
    }

    header('Location: products.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Fetch the product from the database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Display product details in form to edit
        // For example:
        echo "Product Name: " . htmlspecialchars($product['name']);
        // Add your form here for editing the product
    } else {
        echo "Product not found!";
    }
}


// Fetch all products
$all_products = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$all_products->execute();

// Fetch low stock products
$low_stock_products = $conn->prepare("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC");
$low_stock_products->execute();

// Fetch out of stock products
$out_of_stock_products = $conn->prepare("SELECT * FROM products WHERE stock = 0");
$out_of_stock_products->execute();

// Fetch best-selling products (example based on hypothetical `sales_count` column)
$best_selling_products = $conn->prepare("SELECT * FROM products ORDER BY sales_count DESC LIMIT 5");
$best_selling_products->execute();

// Fetch new products (example based on hypothetical `created_at` column)
$new_products = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
$new_products->execute();

// Fetch all categories from the database
$categories = $conn->prepare("SELECT * FROM categories");
$categories->execute();
$categories = $categories->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/sidebar.css">
<style>
    .main {
        padding: 20px;
    }
    .heading {
        text-align: center;
        margin-bottom: 20px;
    }
    /* Landscape table style */
    .section-table {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .section-table .table {
        width: 45%;
        margin-bottom: 20px;
    }
    .table img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
    /* Add Product Form styling */
    .add-product form {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .add-product input, .add-product textarea {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .add-product input[type="submit"] {
        background-color: #28a745;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }
    .add-product input[type="submit"]:hover {
        background-color: #218838;
    }
    .message p {
        color: green;
        text-align: center;
    }

    .add-category table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.add-category table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.add-category th, .add-category td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

.add-category th {
    background-color: #f4f4f4;
}

.add-category td {
    background-color: #fafafa;
}

.add-category input[type="text"], .add-category input[type="submit"] {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    box-sizing: border-box;
}

.add-category .btn-sm {
    font-size: 14px;
    padding: 5px 10px;
}

.add-category .btn-danger {
    background-color: red;
    color: white;
    border: none;
}

.add-category .btn-primary {
    background-color: blue;
    color: white;
    border: none;
}

.add-category th, .add-category td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

.add-category th {
    background-color: #f4f4f4;
}

.add-category td {
    background-color: #fafafa;
}

.add-category .btn-sm {
    font-size: 14px;
    padding: 5px 10px;
}
/* Position the alerts container fixed at the top */
.alerts-container {
    position: fixed; /* Fix it to the top */
    top: 10px; /* Space from the top */
    left: 40;
    right: 0;
    width: 20%;
    z-index: 1000; /* Ensure it stays on top of other content */
    padding: 0;
    max-height: 150px; /* Optional: restricts the height of the alerts container */
    overflow: auto; /* Ensures the alert doesn't overflow if it's too long */
}

.table-container {
    margin-top: 170px; /* Give enough space at the top for the alerts (adjust this value based on your alerts' height) */
}

.table {
    width: 100%; /* Ensure the table takes up full width */
}

/* Optional: Add styling for the alert to make it more noticeable */
.alert {
    margin-bottom: 10px;
}

</style>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main">
    <h2 class="heading">Product Management</h2>

    <div class="section-table">
 <!-- Alerts Container -->
<div class="alerts-container">
    <!-- Fetch low stock products and display alert -->
    <?php
        // Fetch low stock products
        $low_stock_products = $conn->prepare("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC");
        $low_stock_products->execute();
        if ($low_stock_products->rowCount() > 0): ?>
            <div class="alert alert-warning">
                <strong>Low Stock Alert!</strong> Some products have low stock.
                <ul>
                    <?php while ($product = $low_stock_products->fetch(PDO::FETCH_ASSOC)): ?>
                        <li>
                            <?= htmlspecialchars($product['name']); ?> - Stock: <?= htmlspecialchars($product['stock']); ?>
                            <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-outline-warning btn-sm">Edit Product</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
    <?php endif; ?>

    <!-- Fetch out of stock products -->
    <?php
    $out_of_stock_products = $conn->prepare("SELECT * FROM products WHERE stock = 0");
    $out_of_stock_products->execute();
    if ($out_of_stock_products->rowCount() > 0): ?>
        <div class="alert alert-danger">
            <strong>Out of Stock Alert!</strong> Some products are out of stock.
            <ul>
                <?php while ($product = $out_of_stock_products->fetch(PDO::FETCH_ASSOC)): ?>
                    <li>
                        <?= htmlspecialchars($product['name']); ?> - Status: Out of Stock
                        <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-outline-danger btn-sm">Edit Product</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

        <!-- Best Selling Products Table -->
        <table class="table table-bordered">
            <thead>
                <tr><th colspan="3">Best Selling Products</th></tr>
                <tr><th>Image</th><th>Name</th><th>Sales</th></tr>
            </thead>
            <tbody>
                <?php if ($best_selling_products->rowCount() > 0): ?>
                    <?php while ($product = $best_selling_products->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="product image"></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= htmlspecialchars($product['sales_count']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No best-selling products yet!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- New Products Table -->
        <table class="table table-bordered">
            <thead>
                <tr><th colspan="3">New Products</th></tr>
                <tr><th>Image</th><th>Name</th><th>Added On</th></tr>
            </thead>
            <tbody>
                <?php if ($new_products->rowCount() > 0): ?>
                    <?php while ($product = $new_products->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="product image"></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= htmlspecialchars(date("Y-m-d", strtotime($product['created_at']))); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No new products!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Display messages -->
    <?php if (!empty($message)): ?>
        <div class="message">
            <?php foreach ($message as $msg): ?>
                <p><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<!-- All Products Table -->
<table class="table table-bordered">
    <thead>
        <tr><th colspan="5">All Products</th></tr>
        <tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php if ($all_products->rowCount() > 0): ?>
            <?php while ($product = $all_products->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="product image" width="50"></td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td><?= htmlspecialchars($product['price']); ?></td>
                    <td><?= htmlspecialchars($product['stock']); ?></td>
                    <td><?= htmlspecialchars($product['category']); ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- Delete Button -->
                        <a href="products.php?delete_product=<?= $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No products found!</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Archived Products Table -->
<table class="table table-bordered">
    <thead>
        <tr><th colspan="5">Archived Products</th></tr>
        <tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php
        // Fetch archived products
        $archived_products = $conn->prepare("SELECT * FROM products WHERE archived = 1 ORDER BY id DESC");
        $archived_products->execute();

        if ($archived_products->rowCount() > 0):
            while ($product = $archived_products->fetch(PDO::FETCH_ASSOC)):
        ?>
                <tr>
                    <td><img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="product image" width="50"></td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td><?= htmlspecialchars($product['price']); ?></td>
                    <td><?= htmlspecialchars($product['stock']); ?></td>
                    <td><?= htmlspecialchars($product['category']); ?></td>
                    <td>
                        <!-- Restore Button (Optional) -->
                        <a href="products.php?restore_product=<?= $product['id']; ?>" class="btn btn-success btn-sm">Restore</a>
                    </td>
                </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="6">No archived products!</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<section class="add-category">
    <h2>Manage Categories</h2>

    <!-- Categories Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Form to add a new category at the top of the table -->
            <form action="" method="POST">
                <tr>
                    <td><input type="text" name="category_name" placeholder="Category Name" required></td>
                    <td>
                        <input type="submit" name="add_category" value="Add Category" class="btn btn-primary btn-sm">
                    </td>
                </tr>
            </form>

            <?php

            // Check if category is being deleted
            if (isset($_GET['delete_category'])) {
                $category_id = $_GET['delete_category'];
                
                // Prepare and execute delete query
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
                $stmt->bindParam(':id', $category_id);
                $stmt->execute();
            }

            // Fetch all categories from the database
            $categories = $conn->prepare("SELECT * FROM categories");
            $categories->execute();
            $categories = $categories->fetchAll(PDO::FETCH_ASSOC);

            // Display existing categories
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($category['name']) . '</td>';
                    echo '<td>';
                    // Delete button with confirmation
                    echo '<a href="?delete_category=' . $category['id'] . '" onclick="return confirm(\'Are you sure you want to delete this category?\')" class="btn btn-danger btn-sm">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="2">No categories available.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</section>

 <!-- Add New Product Form -->
<section class="add-product">
    <h2>Add New Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <textarea name="details" placeholder="Product Details" required></textarea>
        <input type="number" name="stock" placeholder="Stock Quantity" required>
        <!-- Category Dropdown -->
        <select name="category" required>
            <option value="" disabled selected>Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['name']); ?>"><?= htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" name="add_product" value="Add Product">
    </form>
</section>




</body>
</html>

<?php
include '../components/connect.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details from the database
    $product_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $product_query->execute([$product_id]);
    $product = $product_query->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // If product exists, populate the form with current details
        if (isset($_POST['update_product'])) {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
            $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
            $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

            // Handle image update (if image is uploaded)
            $image = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_folder = "../uploaded_img/$image";

            if ($image) {
                move_uploaded_file($image_tmp, $image_folder);
                $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, details = ?, stock = ?, category = ?, image = ? WHERE id = ?");
                $update_query->execute([$name, $price, $details, $stock, $category, $image, $product_id]);
            } else {
                $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, details = ?, stock = ?, category = ? WHERE id = ?");
                $update_query->execute([$name, $price, $details, $stock, $category, $product_id]);
            }

            // Set a session variable to indicate success
            $_SESSION['product_updated'] = true;
            
            // Redirect to products page after successful update
            header('Location: products.php');
            exit();
        }
    }
}

// Fetch available categories from the database
$categories_query = $conn->prepare("SELECT * FROM categories");
$categories_query->execute();
$categories = $categories_query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
/* Page background and overall container */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Main content box */
.main {
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 80%;
    max-width: 700px;
}

/* Header styling */
h2 {
    text-align: center;
    color: #007bff;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Table layout for the form */
.edit-product-form table {
    width: 100%;
    margin-bottom: 20px;
    border-collapse: separate;
    border-spacing: 0 10px;
}

.edit-product-form td {
    padding: 8px;
    text-align: left;
    vertical-align: middle;
    color: #333;
}

.edit-product-form td strong {
    font-weight: 600;
    color: #007bff;
}

/* Input and select styling */
.form-control {
    width: 100%;
    padding: 10px;
    font-size: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
    outline: none;
}

/* Textarea styling */
.edit-product-form textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* File input styling */
.edit-product-form input[type="file"] {
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #ddd;
}

/* Form buttons styling */
.form-buttons {
    text-align: center;
    margin-top: 20px;
}

.form-buttons .btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s;
}

.form-buttons .btn-success {
    background-color: #28a745;
    border: none;
}

.form-buttons .btn-success:hover {
    background-color: #218838;
}

.form-buttons .btn-secondary {
    background-color: #6c757d;
}

.form-buttons .btn-secondary:hover {
    background-color: #5a6268;
}
    </style>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main">
    <h2>Edit Product</h2>

    <!-- Edit Product Form in a Table Layout -->
    <form action="" method="POST" enctype="multipart/form-data" class="edit-product-form">
        <table class="table table-bordered">
            <tr>
                <td><strong>Product Name</strong></td>
                <td><input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required class="form-control"></td>
            </tr>
            <tr>
                <td><strong>Price</strong></td>
                <td><input type="number" name="price" value="<?= htmlspecialchars($product['price']); ?>" step="0.01" required class="form-control"></td>
            </tr>
            <tr>
                <td><strong>Details</strong></td>
                <td><textarea name="details" required class="form-control"><?= htmlspecialchars($product['details']); ?></textarea></td>
            </tr>
            <tr>
                <td><strong>Stock</strong></td>
                <td><input type="number" name="stock" value="<?= htmlspecialchars($product['stock']); ?>" required class="form-control"></td>
            </tr>
            <tr>
                <td><strong>Category</strong></td>
                <td>
                    <select name="category" required class="form-control">
                        <option value="">Select a Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['name']); ?>" 
                                    <?= $product['category'] === $category['name'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Image</strong></td>
                <td><input type="file" name="image" accept="image/*" class="form-control"></td>
            </tr>
        </table>

        <div class="form-buttons">
            <input type="submit" name="update_product" value="Update Product" class="btn btn-success">
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- JavaScript for alert after update -->
<script>
    // Check if the session has been set for product update
    <?php if (isset($_SESSION['product_updated']) && $_SESSION['product_updated'] == true): ?>
        alert("Product has been successfully updated!");
        <?php unset($_SESSION['product_updated']); // Clear the session flag ?>
    <?php endif; ?>
</script>

</body>
</html>

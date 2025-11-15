<?php
require_once "../includes/db.php";
session_start();

// Admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name) || $price <= 0 || empty($description)) {
        $error = "All fields are required and price must be greater than 0.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please upload a valid product image.";
    } else {
        // Handle image upload
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetDir = "../uploads/product_images/";
        $targetFile = $targetDir . $imageName;
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (!in_array($fileType, $allowedTypes)) {
            $error = "Only JPG, JPEG, PNG, GIF files are allowed.";
        } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Insert into database
            $sql = "INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdss", $name, $price, $description, $imageName);
            if ($stmt->execute()) {
                $success = "Product added successfully.";
            } else {
                $error = "Failed to add product.";
            }
        } else {
            $error = "Failed to upload image.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="add-product-container">
    <h1>Add New Product</h1>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="add-product-form">
        <label>Product Name</label>
        <input type="text" name="name" placeholder="Enter product name" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" placeholder="Enter product price" required>

        <label>Description</label>
        <textarea name="description" placeholder="Enter product description" required></textarea>

        <label>Product Image</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit" class="add-btn">Add Product</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/add_product.css">
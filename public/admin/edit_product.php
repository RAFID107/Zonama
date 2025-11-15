<?php
require_once "../includes/db.php";
session_start();

// Admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get product ID
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit;
}

$product = $result->fetch_assoc();

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $imageName = $product['image']; // Keep current image if not updated

    if (empty($name) || $price <= 0 || empty($description)) {
        $error = "All fields are required and price must be greater than 0.";
    } else {
        // Handle image upload if new file is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetDir = "../uploads/product_images/";
            $targetFile = $targetDir . $imageName;
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Only JPG, JPEG, PNG, GIF files are allowed.";
            } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $error = "Failed to upload image.";
            } else {
                // Optionally delete old image
                if (file_exists($targetDir . $product['image'])) {
                    unlink($targetDir . $product['image']);
                }
            }
        }

        if ($error === "") {
            $sql = "UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssi", $name, $price, $description, $imageName, $product_id);
            if ($stmt->execute()) {
                $success = "Product updated successfully.";
                $product = array_merge($product, ['name' => $name, 'price' => $price, 'description' => $description, 'image' => $imageName]);
            } else {
                $error = "Failed to update product.";
            }
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="edit-product-container">
    <h1>Edit Product</h1>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="edit-product-form">
        <label>Product Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label>Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label>Current Image</label>
        <img src="../uploads/product_images/<?php echo $product['image']; ?>" class="current-image">

        <label>Change Image (optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="update-btn">Update Product</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/edit_product.css">
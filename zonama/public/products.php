<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";

$user = getUser();

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE ? ORDER BY product_id DESC";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $sql = "SELECT * FROM products ORDER BY product_id DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="products-page-container">
    <h1>All Products</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                        <img src="/zonama/uploads/product_images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </a>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price"><?php echo formatPrice($product['price']); ?></p>
                    <a href="cart.php" class="add-to-cart-btn">Add to Cart</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="empty-msg">No products found.</p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/products.css">
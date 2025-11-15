<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";

$user = getUser();

// Fetch featured products (example: latest 8 products)
$sql = "SELECT * FROM products ORDER BY product_id DESC LIMIT 8";
$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to Zonama</h1>
        <p>Your premium online shopping destination</p>
        <a href="products.php" class="shop-now-btn">Shop Now</a>
    </div>
</div>

<div class="featured-products">
    <h2>Featured Products</h2>
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
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/index.css">
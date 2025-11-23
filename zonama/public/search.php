<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";

$user = getUser();

$search = isset($_GET['q']) ? trim($_GET['q']) : "";

if ($search !== "") {
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY product_id DESC";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $products = [];
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="search-container">
    <h1>Search Results for "<?php echo htmlspecialchars($search); ?>"</h1>

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
        <p class="empty-msg">No products found matching your search.</p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/search.css">
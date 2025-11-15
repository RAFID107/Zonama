<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    redirect("products.php");
}

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect("products.php");
}

$product = $result->fetch_assoc();
$user = getUser();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$user) {
        redirect("login.php");
    }

    $quantity = max(1, intval($_POST['quantity']));

    // Check if product already in cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user['user_id'], $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQty = $row['quantity'] + $quantity;
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newQty, $row['cart_id']);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user['user_id'], $product_id, $quantity);
        $stmt->execute();
    }

    redirect("cart.php");
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="product-details-container">
    <div class="product-image">
        <img src="/zonama/uploads/product_images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>

    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="price"><?php echo formatPrice($product['price']); ?></p>
        <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

        <form method="POST" class="add-to-cart-form">
            <label>Quantity:</label>
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/product_details.css">
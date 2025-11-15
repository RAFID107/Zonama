<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
checkLogin(); // Only logged-in users

$user = getUser();

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $qty, $cart_id, $user['user_id']);
        $stmt->execute();
    }
    redirect('cart.php');
}

// Handle remove item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user['user_id']);
    $stmt->execute();
    redirect('cart.php');
}

// Fetch cart items
$sql = "SELECT c.cart_id, p.product_id, p.name, p.price, p.image, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="cart-container">
    <h1>Your Cart</h1>

    <?php if (count($cart_items) > 0): ?>
        <form method="POST" action="">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="/zonama/uploads/product_images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                            </td>
                            <td><?php echo formatPrice($item['subtotal']); ?></td>
                            <td><a href="?remove=<?php echo $item['cart_id']; ?>" class="remove-btn">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-actions">
                <button type="submit" name="update_cart" class="update-btn">Update Cart</button>
                <div class="total-price">
                    <strong>Total:</strong> <?php echo formatPrice($total); ?>
                </div>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        </form>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty. <a href="products.php">Shop Now</a></p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/cart.css">
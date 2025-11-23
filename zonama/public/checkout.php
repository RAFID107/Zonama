<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
checkLogin();

$user = getUser();

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

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (count($cart_items) === 0) {
        $error = "Your cart is empty.";
    } else {
        // Insert order
        $sql = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $user['user_id'], $total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items
        foreach ($cart_items as $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // Clear user's cart
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();

        redirect("order_success.php?order_id=" . $order_id);
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="checkout-container">
    <h1>Checkout</h1>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (count($cart_items) > 0): ?>
        <div class="checkout-details">
            <table class="checkout-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
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
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatPrice($item['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="checkout-total">
                <strong>Total:</strong> <?php echo formatPrice($total); ?>
            </div>

            <form method="POST" action="" class="checkout-form">
                <h3>Shipping Information</h3>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

                <label>Address</label>
                <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>

                <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
            </form>
        </div>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty. <a href="products.php">Shop Now</a></p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/checkout.css">
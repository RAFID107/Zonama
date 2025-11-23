<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkLogin();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
if ($order_id <= 0) {
    header('Location: order.php');
    exit;
}

$user = getUser();

// Fetch order and ensure it belongs to user
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param('ii', $order_id, $user['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: order.php');
    exit;
}

// Fetch items
$items = [];
$sql = "SELECT oi.*, p.name AS product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $items[] = $row;
$stmt->close();

?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-4">
    <h2>Your Order #<?php echo $order['order_id']; ?></h2>
    <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    <p><strong>Total:</strong> <?php echo '৳ ' . number_format($order['total_price'], 2); ?></p>
    <p><strong>Date:</strong> <?php echo $order['created_at']; ?></p>

    <h4 class="mt-3">Items</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($items) === 0): ?>
                    <tr><td colspan="4">No items found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <img src="/zonama/uploads/product_images/<?php echo htmlspecialchars($it['image']); ?>" style="width:60px;height:60px;object-fit:cover;margin-right:8px;" onerror="this.src='/zonama/pics/placeholder.png'">
                                <?php echo htmlspecialchars($it['product_name']); ?>
                            </td>
                            <td><?php echo '৳ ' . number_format($it['price'], 2); ?></td>
                            <td><?php echo intval($it['quantity']); ?></td>
                            <td><?php echo '৳ ' . number_format($it['price'] * $it['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="order.php" class="btn btn-secondary">Back to My Orders</a>
</div>

<?php include "../includes/footer.php"; ?>

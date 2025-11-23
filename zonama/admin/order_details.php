<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['order_id']) ? intval($_GET['order_id']) : 0);
if ($order_id <= 0) {
    header('Location: view_orders.php');
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT o.*, u.name AS user_name, u.email AS user_email FROM orders o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: view_orders.php');
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
    <h2>Order #<?php echo $order['order_id']; ?></h2>
    <p><strong>User:</strong> <?php echo htmlspecialchars($order['user_name'] ?? $order['user_email'] ?? 'Guest'); ?></p>
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
                                <img src="../uploads/product_images/<?php echo htmlspecialchars($it['image']); ?>" style="width:60px;height:60px;object-fit:cover;margin-right:8px;" onerror="this.src='/zonama/pics/placeholder.png'">
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

    <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
</div>

<?php include "../includes/footer.php"; ?>

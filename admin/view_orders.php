<?php
require_once "../includes/db.php";
session_start();

// Admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all orders with user info
$sql = "SELECT o.order_id, o.user_id, o.total_price, o.status, o.created_at, u.name AS user_name, u.email AS user_email
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="orders-container">
    <h1>All Orders</h1>

    <?php if (count($orders) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?> $</td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td><a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="view-btn">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-msg">No orders found.</p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/view_orders.css">
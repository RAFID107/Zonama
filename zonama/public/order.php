<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
checkLogin();

$user = getUser();

// Fetch orders for the current user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="orders-container">
    <h1>My Orders</h1>

    <?php if (count($orders) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo date("d M Y", strtotime($order['created_at'])); ?></td>
                        <td><?php echo formatPrice($order['total_price']); ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td>
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="details-btn">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-msg">You have not placed any orders yet. <a href="products.php">Shop Now</a></p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/order.css">
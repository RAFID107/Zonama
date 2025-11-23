<?php
require_once '../includes/db.php';
session_start();

// Simple admin check (admin login sets $_SESSION['admin_id'])
if (!isset($_SESSION['admin_id'])) {
	header('Location: login.php');
	exit;
}

// Fetch summary metrics
$totals = [];
$res = $conn->query("SELECT COUNT(*) AS total FROM products");
$totals['products'] = ($res) ? intval($res->fetch_assoc()['total']) : 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM users");
$totals['users'] = ($res) ? intval($res->fetch_assoc()['total']) : 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM orders");
$totals['orders'] = ($res) ? intval($res->fetch_assoc()['total']) : 0;
$res = $conn->query("SELECT IFNULL(SUM(total_price),0) AS total FROM orders");
$totals['revenue'] = ($res) ? number_format(floatval($res->fetch_assoc()['total']), 2) : '0.00';

// Recent orders
$recent_orders = [];
$sql = "SELECT o.order_id, o.user_id, o.total_price, o.status, o.created_at, u.name AS user_name, u.email AS user_email
		FROM orders o
		LEFT JOIN users u ON o.user_id = u.user_id
		ORDER BY o.created_at DESC
		LIMIT 10";
$stmt = $conn->prepare($sql);
if ($stmt) {
	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		$recent_orders[] = $row;
	}
	$stmt->close();
}

// Recent products
$recent_products = [];
$res = $conn->query("SELECT product_id, name, price, image, created_at FROM products ORDER BY created_at DESC LIMIT 8");
if ($res) {
	while ($r = $res->fetch_assoc()) $recent_products[] = $r;
}

?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<!-- Load Bootstrap CSS (adds bootstrap-like styles) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-6m5QmKk7x7k9Kq1h6BbZ7lK6v+YV9t6pY3Qm5JwYf3Yv1Gf1W1y2Z3x4a5b6c7d8" crossorigin="anonymous">

<div class="container-fluid mt-4">
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
		<div>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></div>
	</div>

	<div class="row">
		<div class="col-md-3 mb-3">
			<div class="card text-white bg-primary h-100">
				<div class="card-body">
					<div class="card-title">Products</div>
					<h3 class="card-text"><?php echo $totals['products']; ?></h3>
				</div>
				<div class="card-footer">
					<a href="/zonama/admin/add_product.php" class="text-white">Add Product</a>
					&nbsp;|&nbsp;
					<a href="/zonama/public/products.php" class="text-white">View Products</a>
				</div>
			</div>
		</div>

		<div class="col-md-3 mb-3">
			<div class="card text-white bg-success h-100">
				<div class="card-body">
					<div class="card-title">Users</div>
					<h3 class="card-text"><?php echo $totals['users']; ?></h3>
				</div>
				<div class="card-footer">
					<a href="../public/profile.php" class="text-white">View Users</a>
				</div>
			</div>
		</div>

		<div class="col-md-3 mb-3">
			<div class="card text-white bg-warning h-100">
				<div class="card-body">
					<div class="card-title">Orders</div>
					<h3 class="card-text"><?php echo $totals['orders']; ?></h3>
				</div>
				<div class="card-footer">
					<a href="view_orders.php" class="text-white">View Orders</a>
				</div>
			</div>
		</div>

		<div class="col-md-3 mb-3">
			<div class="card text-white bg-info h-100">
				<div class="card-body">
					<div class="card-title">Revenue</div>
					<h3 class="card-text"><?php echo '৳ ' . $totals['revenue']; ?></h3>
				</div>
				<div class="card-footer">
					<a href="view_orders.php" class="text-white">Sales Report</a>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-lg-8 mb-4">
			<div class="card">
				<div class="card-header">Recent Orders</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-striped mb-0">
							<thead>
								<tr>
									<th>Order ID</th>
									<th>User</th>
									<th>Total</th>
									<th>Status</th>
									<th>Date</th>
									<th>Details</th>
								</tr>
							</thead>
							<tbody>
								<?php if (count($recent_orders) === 0): ?>
									<tr><td colspan="6" class="text-center">No recent orders.</td></tr>
								<?php else: ?>
									<?php foreach ($recent_orders as $o): ?>
										<tr>
											<td><?php echo $o['order_id']; ?></td>
											<td><?php echo htmlspecialchars($o['user_name'] ?? $o['user_email'] ?? 'Guest'); ?></td>
											<td><?php echo '৳ ' . number_format($o['total_price'], 2); ?></td>
											<td><?php echo ucfirst($o['status']); ?></td>
											<td><?php echo $o['created_at']; ?></td>
											<td><a href="order_details.php?id=<?php echo $o['order_id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4 mb-4">
			<div class="card">
				<div class="card-header">Recent Products</div>
				<div class="card-body">
					<div class="list-group">
						<?php if (count($recent_products) === 0): ?>
							<div class="text-center">No products yet.</div>
						<?php else: ?>
							<?php foreach ($recent_products as $p): ?>
								<div class="list-group-item d-flex align-items-center">
									<img src="../uploads/product_images/<?php echo htmlspecialchars($p['image']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;margin-right:10px;" onerror="this.src='/zonama/pics/placeholder.png'">
									<div class="flex-fill">
										<div class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></div>
										<small class="text-muted"><?php echo '৳ ' . number_format($p['price'], 2); ?></small>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include "../includes/footer.php"; ?>

<!-- Load Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-PLACEHOLDER" crossorigin="anonymous"></script>


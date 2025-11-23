<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Initialize safe defaults
$user = null;
$cartCount = 0;

// Only try to load a public user and cart when a user session exists
if (isset($_SESSION['user_id'])) {
    $user = getUser();
    if (is_array($user) && isset($user['user_id'])) {
        $cartCount = cartItemCount($conn, $user['user_id']);
    } else {
        $cartCount = 0;
    }
}
?>

<header>
    <a href="/zonama/public/index.php" class="logo">Zonama</a>

    <form action="/zonama/public/search.php" method="GET" class="search-box">
        <input type="text" name="q" placeholder="Search products..." required>
        <button type="submit">Search</button>
    </form>

    <div class="nav-links">
        <?php if (isset($_SESSION['admin_id'])): ?>
            <!-- Admin Links -->
            <a href="/zonama/admin/dashboard.php">Dashboard</a>
            <a href="/zonama/admin/add_product.php">Add Product</a>
            <a href="/zonama/admin/view_orders.php">Orders</a>
            <a href="/zonama/admin/logout.php">Logout</a>
        <?php else: ?>
            <!-- Public Links -->
            <a href="/zonama/public/products.php">Products</a>
            <a href="/zonama/public/cart.php">Cart (<?php echo intval($cartCount); ?>)</a>

            <?php if ($user && is_array($user)): ?>
                <a href="/zonama/public/profile.php"><?php echo htmlspecialchars($user['name']); ?></a>
                <a href="/zonama/public/logout.php">Logout</a>
            <?php else: ?>
                <a href="/zonama/public/login.php">Login</a>
                <a href="/zonama/public/register.php">Register</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</header>

<link rel="stylesheet" href="/zonama/assets/css/navbar.css">
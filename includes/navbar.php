<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Get current user if logged in
$user = getUser();
$cartCount = 0;

if ($user) {
    $cartCount = cartItemCount($conn, $user['user_id']);
}
?>

<header>
    <a href="/zonama/public/index.php" class="logo">Zonama</a>

    <form action="/zonama/public/search.php" method="GET" class="search-box">
        <input type="text" name="q" placeholder="Search products..." required>
        <button type="submit">Search</button>
    </form>

    <div class="nav-links">
        <a href="/zonama/public/products.php">Products</a>
        <a href="/zonama/public/cart.php">Cart (<?php echo $cartCount; ?>)</a>

        <?php if ($user): ?>
            <a href="/zonama/public/profile.php"><?php echo htmlspecialchars($user['name']); ?></a>
            <a href="/zonama/public/logout.php">Logout</a>
        <?php else: ?>
            <a href="/zonama/public/login.php">Login</a>
            <a href="/zonama/public/register.php">Register</a>
        <?php endif; ?>
    </div>
</header>

<link rel="stylesheet" href="/zonama/assets/css/navbar.css">
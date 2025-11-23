<?php
// -----------------------------
// Redirect to another page
// -----------------------------
function redirect($url)
{
    header("Location: $url");
    exit();
}

// -----------------------------
// Check if user is logged in
// -----------------------------
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// -----------------------------
// Format price with currency
// -----------------------------
function formatPrice($amount)
{
    return "৳ " . number_format($amount, 2);
}

// -----------------------------
// Shorten long text for product descriptions
// -----------------------------
function shortenText($text, $limit = 100)
{
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . "...";
    } else {
        return $text;
    }
}

// -----------------------------
// Count items in user's cart
// -----------------------------
function cartItemCount($conn, $user_id)
{
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

// -----------------------------
// Calculate total cart price
// -----------------------------
function cartTotal($conn, $user_id)
{
    $sql = "SELECT c.quantity, p.price 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $total += $row['quantity'] * $row['price'];
    }
    return $total;
}

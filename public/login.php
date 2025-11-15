<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Redirect if already logged in
if (isLoggedIn()) {
    redirect("index.php");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            redirect("index.php");
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}

?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="login-container">
    <h1>Login to Zonama</h1>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="" class="login-form">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit" class="login-btn">Login</button>

        <p class="register-link">Don't have an account? <a href="register.php">Register Here</a></p>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/login.css">
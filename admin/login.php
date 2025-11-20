<?php
require_once '../includes/db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="admin-login-container">
    <h1>Admin Login</h1>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="admin-login-form">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit" class="login-btn">Login</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/admin_login.css">
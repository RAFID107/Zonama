<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
checkLogin();

$user = getUser();
$error = "";
$success = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Simple validation
    if (empty($name) || empty($email)) {
        $error = "Name and Email cannot be empty.";
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user['user_id']);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Update session data
            $_SESSION['name'] = $name;
            $user = getUser();
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="profile-container">
    <h1>My Profile</h1>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" class="profile-form">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

        <label>Address</label>
        <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>

        <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
<link rel="stylesheet" href="/zonama/assets/css/profile.css">
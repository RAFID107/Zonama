<?php
// Make sure session + db connection is available
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

/*
|--------------------------------------------------------------------------
| CHECK USER LOGIN
|--------------------------------------------------------------------------
| Use this to restrict pages only for logged-in users.
| Example: on profile.php → require "auth.php"; checkLogin();
|--------------------------------------------------------------------------
*/
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /zonama/public/login.php");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| CHECK ADMIN ACCESS
|--------------------------------------------------------------------------
| Use this to protect admin pages.
| Example: admin/dashboard.php → require "auth.php"; checkAdmin();
|--------------------------------------------------------------------------
*/
function checkAdmin()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: /zonama/public/admin/login.php");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| GET LOGGED IN USER INFO
|--------------------------------------------------------------------------
| Returns full row from users table of currently logged-in user.
|--------------------------------------------------------------------------
*/
function getUser()
{
    global $conn;
    if (!isset($_SESSION['user_id'])) return null;

    $uid = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/*
|--------------------------------------------------------------------------
| LOGOUT FUNCTION
|--------------------------------------------------------------------------
*/
function logout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: /zonama/public/login.php");
    exit();
}

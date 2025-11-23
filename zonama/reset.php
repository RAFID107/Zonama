<?php
require_once 'config/db.php';

// 1. Reset existing admin (id=1) → password = admin
mysqli_query($conn, "UPDATE admins SET password = '".password_hash('admin', PASSWORD_DEFAULT)."' WHERE admin_id = 1");

// 2. Insert a fresh debug admin → username: debug | password: 1234
mysqli_query($conn, "INSERT INTO admins (username, password, created_at) VALUES ('debug', '".password_hash('1234', PASSWORD_DEFAULT)."', NOW())");

die('Done!<br>→ admin (id=1): password = <b>admin</b><br>→ new user <b>debug</b>: password = <b>1234</b>');
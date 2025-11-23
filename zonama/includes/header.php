<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zonama - Your Premium Online Store</title>
    <link rel="stylesheet" href="/zonama/assets/css/style.css">
    <link rel="stylesheet" href="/zonama/assets/css/header.css">
</head>

<body>
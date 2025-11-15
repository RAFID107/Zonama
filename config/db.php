<?php
$host = "localhost";
$user = "root";        // your MySQL username
$pass = "";            // your MySQL password (usually empty in XAMPP)
$dbname = "zonama";    // database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set UTF-8 encoding
$conn->set_charset("utf8");

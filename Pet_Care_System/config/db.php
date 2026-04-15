<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "petcare_system_db";

// Create connection using procedural style for compatibility
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>

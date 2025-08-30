<?php
$host = 'localhost';
$db   = 'booking';
$user = 'root';
$pass = '';

// MySQLi connection (optional, only if still used somewhere)
$conn = new mysqli($host, $user, $pass, $db);

// PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>

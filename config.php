<?php
// config.php
$host = '127.0.0.1';
$db   = 'sales_dashboard';
$user = 'root';
$pass = ''; // set your password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo 'DB connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}

<?php
/**
 * Database Configuration
 * Luga Pasal - Multi-vendor Clothing Management System
 */

$host = 'localhost';
$db   = 'luga_pasal';
$user = 'root'; // Change this to your DB username
$pass = '';     // Change this to your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Global Constants
define('SITE_NAME', 'Luga Pasal');
define('BASE_URL', 'http://localhost/luga-pasal/');
?>

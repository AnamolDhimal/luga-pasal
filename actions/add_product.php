<?php
session_start();
$config_path = __DIR__ . '/../config/db.php';
if (!file_exists($config_path)) {
    die("Configuration file not found at $config_path");
}
require_once $config_path;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $size = $_POST['size'] ?? '';
    $condition = $_POST['condition_status'] ?? '';
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || $price <= 0 || empty($size) || $stock < 0) {
        header('Location: ../dashboard.php?error=Invalid price or stock provided');
        exit;
    }

    $targetDir = __DIR__ . '/../assets/img/products/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES["image"]["name"] ?? 'default.jpg');
    $targetFilePath = $targetDir . $fileName;

    if (isset($_FILES["image"]) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (vendor_id, category_id, title, description, price, size, condition_status, stock, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $categoryId, $title, $description, $price, $size, $condition, $stock, $targetFilePath]);
            header('Location: ../dashboard.php?success=Product added successfully!');
            exit;
        } catch (Exception $e) {
            header('Location: ../dashboard.php?error=Database error: ' . $e->getMessage());
            exit;
        }
    } else {
        header('Location: ../dashboard.php?error=Image upload failed');
        exit;
    }
} else {
    header('Location: ../login.php');
    exit;
}
?>

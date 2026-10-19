<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $categoryId = $_POST['category_id'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $condition = $_POST['condition_status'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Handle Image Upload
    $targetDir = "assets/img/products/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (vendor_id, category_id, title, description, price, size, condition_status, stock, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $categoryId, $title, $description, $price, $size, $condition, $stock, $targetFilePath]);
            header('Location: dashboard.php?success=1');
            exit;
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        die("Image upload failed.");
    }
} else {
    die("Unauthorized access.");
}
?>

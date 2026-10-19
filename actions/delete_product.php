<?php
session_start();
require_once 'config/db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $productId = (int)$_GET['id'];
    $userId = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT vendor_id FROM products WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product && $product['vendor_id'] == $userId) {
        $del = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $del->execute([$productId]);
        header('Location: dashboard.php?deleted=1');
        exit;
    }
}
header('Location: dashboard.php');
?>

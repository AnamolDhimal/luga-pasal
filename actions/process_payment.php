<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $buyerId = $_SESSION['user_id'];
    $totalAmount = $_POST['total_amount'];
    $paymentMethod = $_POST['payment_method'];
    $shippingAddress = $_POST['shipping_address'];

    try {
        $pdo->beginTransaction();

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, total_amount, payment_method, shipping_address, payment_status) VALUES (?, ?, ?, ?, 'completed')");
        $stmt->execute([$buyerId, $totalAmount, $paymentMethod, $shippingAddress]);
        $orderId = $pdo->lastInsertId();

        // 2. Process Items and Update Stock
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $productId => $qty) {
                // Add to order_items
                $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) SELECT ?, product_id, ?, price FROM products WHERE product_id = ?");
                $stmtItem->execute([$orderId, $qty, $productId]);

                // Update Inventory
                $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
                $stmtStock->execute([$qty, $productId]);
            }
        }

        $pdo->commit();
        unset($_SESSION['cart']);

        // Generate Receipt (simplified as a redirect with order ID)
        header("Location: ../receipt.php?order_id=$orderId");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Transaction failed: " . $e->getMessage());
    }
} else {
    die("Unauthorized access or invalid request.");
}
?>

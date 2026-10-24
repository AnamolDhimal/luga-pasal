<?php
session_start();
require_once '../config/db.php';

// Get product ID from URL
$productId = isset($_GET['add']) ? (int)$_GET['add'] : 0;

if ($productId > 0) {
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add to cart (increment quantity if exists)
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }

    // Redirect back to index with a success message
    header('Location: ../index.php?added=1');
    exit;
} else {
    header('Location: ../index.php');
    exit;
}
?>

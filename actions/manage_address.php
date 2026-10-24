<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Handle deletions
if (isset($_GET['delete'])) {
    $addrId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM buyer_addresses WHERE address_id = ? AND user_id = ?");
    $stmt->execute([$addrId, $userId]);
    header('Location: profile.php');
    exit;
}

// Handle new address addition
if (isset($_GET['add']) && $_GET['add'] === '1') {
    // In a real app, this would be a form. For this demo, we'll add a dummy address.
    $stmt = $pdo->prepare("INSERT INTO buyer_addresses (user_id, address_line1, city, postal_code) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, '123 Sample Street', 'Kathmandu', '44600']);
    header('Location: profile.php?address_added=1');
    exit;
}

header('Location: profile.php');
exit;
?>

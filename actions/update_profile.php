<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

try {
    // 1. Update basic user info
    if (isset($_POST['name']) || isset($_POST['phone'])) {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE user_id = ?");
        $stmt->execute([$name, $phone, $userId]);
    }

    // 2. Update vendor profile if applicable
    if (($userRole === 'brand' || $userRole === 'casual_seller') && isset($_POST['store_name'])) {
        $storeName = trim($_POST['store_name']);
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $contactInfo = trim($_POST['contact_info'] ?? '');

        $stmt = $pdo->prepare("INSERT INTO vendor_profiles (vendor_id, store_name, description, location, contact_info)
                               VALUES (?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE store_name = ?, description = ?, location = ?, contact_info = ?");
        $stmt->execute([$userId, $storeName, $description, $location, $contactInfo, $storeName, $description, $location, $contactInfo]);
    }

    header('Location: profile.php?success=Profile updated successfully!');
} catch (Exception $e) {
    header('Location: profile.php?error=Update failed: ' . $e->getMessage());
}
exit;
?>

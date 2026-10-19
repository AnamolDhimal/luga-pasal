<?php
session_start();
require_once 'config/db.php';

// Generic Auth Check Middleware
function require_role($allowed_roles) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("Access Denied: You do not have the required permissions to view this page.");
    }
}

// Header Component
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="bg-blue-600 text-white text-xs py-2 px-4 text-center">
            Welcome to Luga Pasal - The Ultimate Clothing Hub
        </div>
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-2">
                <img src="logo/luga-pasal-logo.svg" alt="Logo" class="h-10">
                <span class="text-2xl font-bold text-blue-800">Luga Pasal</span>
            </a>

            <div class="flex-1 mx-8 hidden md:block">
                <form action="index.php" method="GET" class="flex w-full max-w-xl mx-auto">
                    <input type="text" name="search" placeholder="Search for clothes, brands..." class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700 transition">Search</button>
                </form>
            </div>

            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-4">
                        <a href="dashboard.php" class="text-gray-600 hover:text-blue-600 font-medium">My Account</a>
                        <a href="actions/logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
                        <div class="relative cursor-pointer group">
                            <span class="text-gray-600 hover:text-blue-600">
                                🛒 Cart
                                <span class="absolute -top-2 -right-3 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                                </span>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-blue-600 font-medium">Login</a>
                    <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition font-medium">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>

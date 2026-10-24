<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';
require_once 'config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Bold Street-Style Apparel</title>
    <link rel="icon" href="logo/luga-pasal-icon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandOrange: '#FF5A0A',
                        brandLime: '#CCFF00',
                        matteBlack: '#0A0A0A',
                    },
                    fontFamily: {
                        sans: ['Roboto', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap');
        .category-link {
            position: relative;
            transition: color 0.3s;
        }
        .category-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #CCFF00;
            transition: width 0.3s;
        }
        .category-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-white text-matteBlack font-sans">
    <!-- TOP UTILITY BAR -->
    <div class="bg-matteBlack text-white h-10 flex items-center justify-between px-4 text-[12px] md:text-[14px] font-normal uppercase tracking-wide border-b border-brandLime">
        <div class="hidden sm:flex items-center space-x-6 md:space-x-8">
            <a href="dashboard.php" class="hover:text-brandLime transition-colors"><?php echo __('become_seller'); ?></a>
            <a href="#" class="hover:text-brandLime transition-colors"><?php echo __('help_support'); ?></a>
        </div>

        <div class="flex items-center space-x-4 md:space-x-6">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="hover:text-brandLime transition-colors text-sm font-medium">My Profile</a>
                <a href="actions/logout.php" class="hover:text-brandLime transition-colors text-sm font-medium">Logout</a>
            <?php else: ?>
                <a href="login.php" class="hover:text-brandLime transition-colors"><?php echo __('login'); ?></a>
                <a href="register.php" class="hover:text-brandLime transition-colors"><?php echo __('signup'); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN HEADER -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 md:py-4 flex items-center justify-between">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2 md:space-x-3 group shrink-0">
                <div class="bg-matteBlack p-1 rounded-md flex items-center justify-center w-8 h-8 md:w-12 md:h-12">
                    <img src="logo/luga-pasal-icon.svg" alt="LP Icon" class="h-6 w-6 md:h-8 md:w-8">
                </div>
                <span class="text-lg md:text-2xl font-black tracking-tighter text-matteBlack uppercase group-hover:text-gray-700 transition">
                    Luga Pasal
                </span>
            </a>

            <!-- Search Bar -->
            <div class="flex-1 mx-4 md:mx-10 hidden sm:block">
                <form action="index.php" method="GET" class="relative max-w-xl mx-auto">
                    <input type="text" name="search" placeholder="<?php echo __('search_placeholder'); ?>" class="w-full px-4 py-2 bg-gray-100 border-transparent rounded-full focus:bg-white focus:ring-2 focus:ring-brandLime focus:outline-none transition-all text-sm">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-matteBlack">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Icons -->
            <div class="flex items-center space-x-3 md:space-x-5 shrink-0">
                <a href="#" class="text-matteBlack hover:text-gray-600 transition relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>
                <a href="checkout.php" class="text-matteBlack hover:text-gray-600 transition relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5" />
                    </svg>
                    <span class="absolute -top-2 -right-2 bg-brandLime text-black text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border border-black">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
            </div>
        </div>

        <!-- CATEGORY NAVIGATION -->
        <nav class="bg-white border-t border-gray-50 overflow-x-auto">
            <div class="container mx-auto px-4">
                <ul class="flex items-center justify-center space-x-8 py-3 text-sm font-bold uppercase tracking-widest whitespace-nowrap">
                    <li><a href="index.php" class="category-link text-matteBlack hover:text-gray-600"><?php echo __('new_arrivals'); ?></a></li>
                    <li><a href="index.php?category=menswear" class="category-link text-matteBlack hover:text-gray-600"><?php echo __('men'); ?></a></li>
                    <li><a href="index.php?category=womenswear" class="category-link text-matteBlack hover:text-gray-600"><?php echo __('women'); ?></a></li>
                    <li><a href="index.php?category=cultural" class="category-link text-matteBlack hover:text-gray-600"><?php echo __('streetwear'); ?></a></li>
                    <li><a href="index.php?sort=sale" class="category-link text-red-600 hover:text-red-700"><?php echo __('sale'); ?></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main>

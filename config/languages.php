<?php
$languages = [
    'en' => [
        'welcome' => 'Welcome to Luga Pasal - The Ultimate Clothing Hub',
        'become_seller' => 'Become a Seller',
        'help_support' => 'Help & Support',
        'login' => 'Login',
        'signup' => 'Sign Up',
        'search_placeholder' => 'Search for street-style apparel...',
        'my_account' => 'My Account',
        'logout' => 'Logout',
        'cart' => 'Cart',
        'new_arrivals' => 'New Arrivals',
        'men' => 'Men',
        'women' => 'Women',
        'streetwear' => 'Streetwear',
        'sale' => 'Sale',
        'hero_title' => 'Upgrade Your Wardrobe Today!',
        'hero_subtitle' => 'Get the best deals on Cultural, Menswear and Womenswear.',
        'shop_now' => 'Shop Now',
        'sell_promo_title' => 'Got Clothes to Sell?',
        'sell_promo_text' => 'Turn your pre-loved outfits into cash. Fast, easy, and secure.',
        'become_seller_btn' => 'Become a Seller',
        'explore_title' => 'Explore Clothing',
        'no_products' => 'No products found matching your criteria.',
        'add_to_cart' => 'Add to Cart',
        'lang_change' => 'भाषा परिवर्तन',
    ],
    'np' => [
        'welcome' => 'लुगा Pasal मा स्वागत छ - उत्तम लुगाहरूको केन्द्र',
        'become_seller' => 'विक्रेता बन्नुहोस्',
        'help_support' => 'सहयोग र सहायता',
        'login' => 'लग-इन',
        'signup' => 'साइन-अप',
        'search_placeholder' => 'लुगाहरू खोज्नुहोस्...',
        'my_account' => 'मेरो खाता',
        'logout' => 'लग-आउट',
        'cart' => 'कार्ट',
        'new_arrivals' => 'नयाँ आगमन',
        'men' => 'पुरुष',
        'women' => 'महिला',
        'streetwear' => 'स्ट्रीटवेयर',
        'sale' => 'सेल',
        'hero_title' => 'आजै आफ्नो वार्डरोब अपग्रेड गर्नुहोस्!',
        'hero_subtitle' => 'सांस्कृतिक, पुरुष र महिला लुगाहरूमा उत्कृष्ट डिलहरू पाउनुहोस्।',
        'shop_now' => 'अहिले किनमेल गर्नुहोस्',
        'sell_promo_title' => 'लुगा बेच्न चाहनुहुन्छ?',
        'sell_promo_text' => 'तपाईंका पुराना लुगाहरूलाई नगदमा बदल्नुहोस्। छिटो, सजिलो र सुरक्षित।',
        'become_seller_btn' => 'विक्रेता बन्नुहोस्',
        'explore_title' => 'लुगाहरू खोज्नुहोस्',
        'no_products' => 'तपाईंको खोज अनुसार कुनै लुगा फेला परेन।',
        'add_to_cart' => 'कार्टमा थप्नुहोस्',
        'lang_change' => 'Change Language',
    ]
];

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Change language if requested
if (isset($_GET['lang'])) {
    if (in_array($_GET['lang'], ['en', 'np'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

$lang = $_SESSION['lang'];
function __($key) {
    global $languages;
    $current_lang = $_SESSION['lang'] ?? 'en';
    return $languages[$current_lang][$key] ?? $languages['en'][$key] ?? $key;
}
?>

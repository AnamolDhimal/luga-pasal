<?php
require_once 'config/db.php';

$category = $_GET['category'] ?? '';
$size = $_GET['size'] ?? '';
$condition = $_GET['condition'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$query = "SELECT p.*, u.name as vendor_name FROM products p JOIN users u ON p.vendor_id = u.user_id WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
}
if ($size) {
    $query .= " AND p.size = ?";
    $params[] = $size;
}
if ($condition) {
    $query .= " AND p.condition_status = ?";
    $params[] = $condition;
}
if ($minPrice !== '') {
    $query .= " AND p.price >= ?";
    $params[] = $minPrice;
}
if ($maxPrice !== '') {
    $query .= " AND p.price <= ?";
    $params[] = $maxPrice;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

if (empty($products)) {
    echo '<div class="col-span-full text-center py-20">
            <img src="logo/luga-pasal-icon.svg" class="h-20 w-20 mx-auto opacity-20 mb-4" alt="no-products">
            <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
          </div>';
    exit;
}

foreach($products as $p) {
    $imgPath = $p['image_path'] ?: 'assets/img/placeholder.jpg';
    echo '
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="relative h-56 bg-gray-100 overflow-hidden">
            <img src="'.$imgPath.'" alt="'.$p['title'].'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">'.$p['condition_status'].'</span>
                <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">'.$p['size'].'</span>
            </div>
        </div>
        <div class="p-4 space-y-2">
            <p class="text-xs text-gray-400 font-medium truncate">'.$p['vendor_name'].'</p>
            <h3 class="text-sm font-semibold text-gray-800 truncate">'.$p['title'].'</h3>
            <div class="flex items-center justify-between">
                <span class="text-blue-600 font-bold text-lg">Rs. '.number_format($p['price'], 2).'</span>
                <span class="text-xs text-gray-500">'.$p['stock'].' in stock</span>
            </div>
            <button class="w-full py-2 bg-blue-600 text-white rounded-md text-xs font-bold hover:bg-blue-700 transition shadow-sm">Add to Cart</button>
        </div>
    </div>';
}
?>

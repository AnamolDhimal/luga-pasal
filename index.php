<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// Fetch a few categories for the filter bar
$catStmt = $pdo->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll();

// Basic Product Fetching (will be enhanced with AJAX filtering later)
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT p.*, u.name as vendor_name FROM products p JOIN users u ON p.vendor_id = u.user_id WHERE p.title LIKE ? OR p.description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT p.*, u.name as vendor_name FROM products p JOIN users u ON p.vendor_id = u.user_id ORDER BY p.created_at DESC");
}
$products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="container mx-auto px-4 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        <!-- Left: Promotional Banner Slider (Simplified as a Static Banner for now) -->
        <div class="relative overflow-hidden rounded-xl bg-matteBlack h-64 md:h-80 flex items-center px-12 text-white">
            <div class="z-10 space-y-4">
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">Upgrade Your <br>Wardrobe Today!</h1>
                <p class="text-lg opacity-90">Get the best deals on Cultural, Menswear and Womenswear.</p>
                <a href="#products" class="inline-block bg-brandLime text-black px-6 py-3 rounded-lg font-bold hover:bg-lime-400 transition transform hover:scale-105">Shop Now</a>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 pointer-events-none">
                <img src="logo/luga-pasal-logo.svg" class="w-64 h-64 brightness-0 invert" alt="background-deco">
            </div>
        </div>
    </div>
</div>

<!-- Advanced Filtering Bar -->
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-wrap items-center gap-6">
        <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold text-gray-500 uppercase">Category:</span>
            <select id="filter-category" class="px-3 py-1 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Categories</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['category_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold text-gray-500 uppercase">Size:</span>
            <select id="filter-size" class="px-3 py-1 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Sizes</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="XXL">XXL</option>
            </select>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold text-gray-500 uppercase">Condition:</span>
            <select id="filter-condition" class="px-3 py-1 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All</option>
                <option value="new">New</option>
                <option value="pre-loved">Pre-loved</option>
            </select>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold text-gray-500 uppercase">Price Range:</span>
            <div class="flex items-center space-x-2">
                <input type="number" id="filter-min-price" placeholder="Min" class="w-20 px-2 py-1 border rounded-md text-sm outline-none">
                <span>-</span>
                <input type="number" id="filter-max-price" placeholder="Max" class="w-20 px-2 py-1 border rounded-md text-sm outline-none">
            </div>
        </div>
        <button id="apply-filters" class="bg-gray-100 text-gray-700 px-4 py-1 rounded-md text-sm font-medium hover:bg-gray-200 transition ml-auto">Apply Filters</button>
    </div>
</div>

<!-- Product Grid -->
<div class="container mx-auto px-4 mb-20" id="products">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Explore Clothing</h2>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>Sort by:</span>
            <select class="border-none bg-transparent font-medium text-gray-800 focus:ring-0">
                <option>Latest Arrivals</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
            </select>
        </div>
    </div>

    <div id="product-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        <?php if (empty($products)): ?>
            <div class="col-span-full text-center py-20">
                <img src="logo/luga-pasal-icon.svg" class="h-20 w-20 mx-auto opacity-20 mb-4" alt="no-products">
                <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
            </div>
        <?php else: ?>
            <?php foreach($products as $p): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="relative h-56 bg-gray-100 overflow-hidden">
                        <img src="<?php echo $p['image_path'] ?: 'assets/img/placeholder.jpg'; ?>" alt="<?php echo $p['title']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 left-2 flex flex-col gap-1">
                            <span class="bg-brandLime text-black text-[10px] font-bold px-2 py-1 rounded uppercase border border-black"><?php echo $p['condition_status']; ?></span>
                            <span class="bg-matteBlack text-white text-[10px] font-bold px-2 py-1 rounded uppercase"><?php echo $p['size']; ?></span>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <p class="text-xs text-gray-400 font-medium truncate"><?php echo $p['vendor_name']; ?></p>
                        <h3 class="text-sm font-semibold text-gray-800 truncate"><?php echo $p['title']; ?></h3>
                        <div class="flex items-center justify-between">
                            <span class="text-matteBlack font-bold text-lg">Rs. <?php echo number_format($p['price'], 2); ?></span>
                            <span class="text-xs text-gray-500"><?php echo $p['stock']; ?> in stock</span>
                        </div>
                        <button onclick="window.location.href='http://localhost/luga-pasal/actions/add_to_cart.php?add=<?php echo $p['product_id']; ?>'" class="w-full py-2 bg-brandLime text-black rounded-md text-xs font-bold hover:bg-lime-400 transition shadow-sm border border-black">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
    <script src="assets/js/filter.js"></script>

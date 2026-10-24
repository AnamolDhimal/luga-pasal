<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// Role Check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800">
            <?php
                if($userRole === 'admin') echo 'Administrator Control Panel';
                elseif($userRole === 'brand') echo 'Brand Store Dashboard';
                else echo 'Seller Console';
            ?>
        </h1>
        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
            Role: <?php echo str_replace('_', ' ', $userRole); ?>
        </span>
    </div>

    <?php if ($userRole === 'admin'): ?>
        <!-- ADMIN VIEW -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-600">
                <p class="text-gray-500 text-sm font-medium">Total Users</p>
                <h3 class="text-3xl font-bold text-gray-800">
                    <?php echo $pdo->query("SELECT count(*) FROM users")->fetchColumn(); ?>
                </h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600">
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <h3 class="text-3xl font-bold text-gray-800">
                    <?php echo $pdo->query("SELECT count(*) FROM orders")->fetchColumn(); ?>
                </h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-600">
                <p class="text-gray-500 text-sm font-medium">Global Revenue</p>
                <h3 class="text-3xl font-bold text-gray-800">
                    Rs. <?php echo number_format($pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?: 0, 2); ?>
                </h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b font-bold text-gray-700">Global Order Logs</div>
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">Order ID</th>
                        <th class="px-6 py-3">Buyer</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Method</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php
                    $orders = $pdo->query("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.user_id ORDER BY o.created_at DESC");
                    while($row = $orders->fetch()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">#<?php echo $row['order_id']; ?></td>
                            <td class="px-6 py-4"><?php echo $row['buyer_name']; ?></td>
                            <td class="px-6 py-4 font-bold">Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td class="px-6 py-4 uppercase"><?php echo $row['payment_method']; ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold <?php echo $row['payment_status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                    <?php echo $row['payment_status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($userRole === 'brand' || $userRole === 'casual_seller'): ?>
        <!-- VENDOR VIEW -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Actions -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-bold text-matteBlack">Inventory Management</h3>
                    <button onclick="document.getElementById('product-modal').classList.remove('hidden')" class="w-full bg-brandLime text-black py-3 rounded-lg font-bold hover:bg-lime-400 transition text-sm border border-black shadow-sm">
                        + Add New Clothing
                    </button>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border space-y-4">
                    <h3 class="font-bold text-gray-800">Store Stats</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">My Sales:</span>
                            <span class="font-bold text-gray-800">Rs. <?php
                                $stmt = $pdo->prepare("SELECT SUM(o.total_amount) FROM orders o JOIN order_items oi ON o.order_id = oi.order_id JOIN products p ON oi.product_id = p.product_id WHERE p.vendor_id = ?");
                                $stmt->execute([$userId]);
                                echo number_format($stmt->fetchColumn() ?: 0, 2);
                            ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Items Sold:</span>
                            <span class="font-bold text-gray-800">
                                <?php
                                $stmt = $pdo->prepare("SELECT SUM(oi.quantity) FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE p.vendor_id = ?");
                                $stmt->execute([$userId]);
                                echo $stmt->fetchColumn() ?: 0;
                            ?>
                                </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b font-bold text-gray-700">My Clothing Listings</div>
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Price</th>
                                <th class="px-6 py-3">Stock</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM products WHERE vendor_id = ?");
                            $stmt->execute([$userId]);
                            while($p = $stmt->fetch()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="<?php echo $p['image_path'] ?: 'assets/img/placeholder.jpg'; ?>" class="h-10 w-10 object-cover rounded">
                                        <span class="font-medium"><?php echo $p['title']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 font-bold">Rs. <?php echo number_format($p['price'], 2); ?></td>
                                    <td class="px-6 py-4"><?php echo $p['stock']; ?></td>
                                    <td class="px-6 py-4 space-x-2">
                                        <a href="actions/delete_product.php?id=<?php echo $p['product_id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add Product Modal -->
<div id="product-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-8 relative">
        <button onclick="document.getElementById('product-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        <h3 class="text-2xl font-bold text-gray-800 mb-6">List New Clothing</h3>

        <form action="actions/add_product.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Title</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                        <?php
                        $cats = $pdo->query("SELECT * FROM categories");
                        while($c = $cats->fetch()): ?>
                            <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price (Rs.)</label>
                    <input type="number" step="0.01" name="price" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Size</label>
                    <select name="size" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Condition</label>
                    <select name="condition_status" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="new">New</option>
                        <option value="pre-loved">Pre-loved</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Image</label>
                    <input type="file" name="image" accept="image/*" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="w-full px-3 py-2 border rounded-md h-24 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="md:col-span-2 pt-4">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">List Product Now</button>
            </div>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

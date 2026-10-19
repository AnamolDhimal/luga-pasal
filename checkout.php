<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// Initialize Cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart (simple mock for this turn)
if (isset($_GET['add'])) {
    $productId = (int)$_GET['add'];
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
    header('Location: index.php?added=1');
    exit;
}

// Calculate Total
$total = 0;
$cartItems = [];

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT p.*, u.name as vendor_name FROM products p JOIN users u ON p.vendor_id = u.user_id WHERE p.product_id IN ($placeholders)");
    $stmt->execute($ids);
    $cartItems = $stmt->fetchAll();

    foreach ($cartItems as $item) {
        $total += $item['price'] * $_SESSION['cart'][$item['product_id']];
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">Your Checkout</h2>

    <?php if (empty($cartItems)): ?>
        <div class="bg-white p-12 rounded-xl shadow-sm border text-center">
            <p class="text-gray-500 text-lg mb-6">Your cart is empty!</p>
            <a href="index.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Order Summary -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b font-bold text-gray-700">Order Summary</div>
                    <div class="divide-y">
                        <?php foreach($cartItems as $item): ?>
                            <div class="p-6 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <img src="<?php echo $item['image_path'] ?: 'assets/img/placeholder.jpg'; ?>" class="h-16 w-16 object-cover rounded border">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?php echo $item['title']; ?></h4>
                                        <p class="text-xs text-gray-500">Vendor: <?php echo $item['vendor_name']; ?> | Size: <?php echo $item['size']; ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600">Rs. <?php echo number_format($item['price'], 2); ?></p>
                                    <p class="text-xs text-gray-500">Qty: <?php echo $_SESSION['cart'][$item['product_id']]; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Payment & Shipping -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border space-y-6">
                    <h3 class="text-xl font-bold text-gray-800">Payment Method</h3>

                    <form action="actions/process_payment.php" method="POST" class="space-y-4">
                        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

                        <div class="space-y-3">
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="cod" checked class="w-4 h-4 text-blue-600">
                                <span class="ml-3 font-medium text-gray-700">Cash on Delivery (COD)</span>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="esewa" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 font-medium text-gray-700">eSewa Wallet</span>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="khalti" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 font-medium text-gray-700">Khalti Wallet</span>
                            </label>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Shipping Address</label>
                            <textarea name="shipping_address" required class="w-full px-3 py-2 border rounded-md h-24 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Enter full delivery address..."></textarea>
                        </div>

                        <div class="pt-4 border-t">
                            <div class="flex justify-between text-xl font-bold text-gray-800 mb-6">
                                <span>Total Amount</span>
                                <span>Rs. <?php echo number_format($total, 2); ?></span>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg text-lg">Confirm Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

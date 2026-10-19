<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

$orderId = $_GET['order_id'] ?? 0;

$stmt = $pdo->prepare("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.user_id WHERE o.order_id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

$stmtItems = $pdo->prepare("SELECT oi.*, p.title, p.size FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
$stmtItems->execute([$orderId]);
$items = $stmtItems->fetchAll();
?>

<div class="container mx-auto px-4 py-12 max-w-2xl">
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-200 text-center">
        <div class="mb-6">
            <img src="logo/luga-pasal-logo.svg" class="h-16 mx-auto mb-4" alt="Logo">
            <h2 class="text-3xl font-extrabold text-gray-800">Order Confirmed!</h2>
            <p class="text-gray-500">Thank you for shopping with Luga Pasal. Your order is on its way.</p>
        </div>

        <div class="bg-gray-50 p-6 rounded-xl border border-dashed border-gray-300 text-left space-y-4">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Order ID:</span>
                <span class="font-bold text-gray-800">#<?php echo $order['order_id']; ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Customer:</span>
                <span class="font-bold text-gray-800"><?php echo $order['buyer_name']; ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Payment Method:</span>
                <span class="font-bold text-gray-800 uppercase"><?php echo $order['payment_method']; ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Delivery Address:</span>
                <span class="font-bold text-gray-800"><?php echo $order['shipping_address']; ?></span>
            </div>

            <div class="mt-6">
                <h4 class="font-bold text-gray-700 mb-3">Items Purchased:</h4>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-2">Item</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                            <tr class="border-b last:border-0">
                                <td class="py-2"><?php echo $item['title']; ?> (<?php echo $item['size']; ?>)</td>
                                <td class="py-2 text-center"><?php echo $item['quantity']; ?></td>
                                <td class="py-2 text-right">Rs. <?php echo number_format($item['unit_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between text-2xl font-bold text-blue-700">
                <span>Total Paid:</span>
                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>

        <div class="mt-8 space-y-4">
            <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-900 transition">Print Invoice</button>
            <a href="index.php" class="block text-blue-600 font-semibold hover:underline">Continue Shopping</a>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

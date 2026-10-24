<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Fetch basic user info
$stmt = $pdo->prepare("SELECT name, email, phone, profile_pic FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<div class="container mx-auto px-4 py-12 max-w-5xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <!-- Left Column: Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="relative w-32 h-32 mx-auto mb-6">
                    <img src="<?php echo $user['profile_pic'] ?: 'assets/img/default-profile.jpg'; ?>"
                         class="w-full h-full object-cover rounded-full border-4 border-brandLime shadow-lg">
                </div>
                <h2 class="text-2xl font-bold text-matteBlack"><?php echo $user['name']; ?></h2>
                <p class="text-gray-500 text-sm mb-6"><?php echo $user['email']; ?></p>

                <div class="space-y-3 text-left bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Role:</span>
                        <span class="font-bold uppercase text-xs"><?php echo str_replace('_', ' ', $userRole); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Phone:</span>
                        <span class="font-medium"><?php echo $user['phone'] ?: 'Not provided'; ?></span>
                    </div>
                </div>

                <a href="actions/update_profile.php" class="block mt-8 w-full py-3 bg-matteBlack text-white rounded-xl font-bold hover:bg-gray-800 transition text-sm">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Right Column: Role-Based Content -->
        <div class="lg:col-span-2 space-y-8">

            <?php if ($userRole === 'buyer'): ?>
                <!-- BUYER VIEW -->
                <div class="space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">📍</span>
                            Shipping Addresses
                        </h3>
                        <div id="address-list" class="space-y-4">
                            <?php
                            $addrStmt = $pdo->prepare("SELECT * FROM buyer_addresses WHERE user_id = ?");
                            $addrStmt->execute([$userId]);
                            $addresses = $addrStmt->fetchAll();
                            if ($addresses):
                                foreach($addresses as $addr): ?>
                                    <div class="p-4 border rounded-xl flex justify-between items-center hover:bg-gray-50">
                                        <div>
                                            <p class="font-medium"><?php echo $addr['address_line1']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $addr['city'] . ', ' . $addr['postal_code']; ?></p>
                                            <?php if($addr['is_default']): ?>
                                                <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-full font-bold">Default</span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="actions/manage_address.php?delete=<?php echo $addr['address_id']; ?>" class="text-red-400 hover:text-red-600 text-xs">Remove</a>
                                    </div>
                                <?php endforeach; else: ?>
                                    <p class="text-gray-400 text-sm italic">No addresses saved yet.</p>
                                <?php endif; ?>
                        </div>
                        <a href="actions/manage_address.php?add=1" class="mt-6 inline-block text-sm font-bold text-blue-600 hover:underline">+ Add New Address</a>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <span class="bg-green-100 text-green-600 p-2 rounded-lg mr-3">📦</span>
                            My Order History
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Order ID</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Total</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <?php
                                    $orders = $pdo->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC");
                                    $orders->execute([$userId]);
                                    while($o = $orders->fetch()): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">#<?php echo $o['order_id']; ?></td>
                                            <td class="px-4 py-3 text-gray-500"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                            <td class="px-4 py-3 font-bold">Rs. <?php echo number_format($o['total_amount'], 2); ?></td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-[10px] font-bold <?php echo $o['payment_status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                                    <?php echo $o['payment_status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($userRole === 'brand' || $userRole === 'casual_seller'): ?>
                <!-- SELLER / BRAND VIEW -->
                <div class="space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <span class="bg-lime-100 text-lime-600 p-2 rounded-lg mr-3">🏢</span>
                            Store Profile
                        </h3>
                        <?php
                        $stmtStore = $pdo->prepare("SELECT * FROM vendor_profiles WHERE vendor_id = ?");
                        $stmtStore->execute([$userId]);
                        $store = $stmtStore->fetch() ?: [];
                        ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Store Name</p>
                                <p class="text-lg font-semibold"><?php echo $store['store_name'] ?? 'Unnamed Store'; ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Location</p>
                                <p class="text-lg font-semibold"><?php echo $store['location'] ?? 'Not specified'; ?></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">About Store</p>
                                <p class="text-gray-600"><?php echo $store['description'] ?? 'No description provided.'; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <span class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-3">📊</span>
                            Inventory Audit (Sold vs Unsold)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3">Current Stock</th>
                                        <th class="px-4 py-3">Total Sold</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <?php
                                    $prodStmt = $pdo->prepare("SELECT * FROM products WHERE vendor_id = ?");
                                    $prodStmt->execute([$userId]);
                                    while($p = $prodStmt->fetch()):
                                        $soldStmt = $pdo->prepare("SELECT SUM(oi.quantity) FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE oi.product_id = ? AND o.payment_status = 'completed'");
                                        $soldStmt->execute([$p['product_id']]);
                                        $sold = $soldStmt->fetchColumn() ?: 0;
                                    ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium"><?php echo $p['title']; ?></td>
                                            <td class="px-4 py-3"><?php echo $p['stock']; ?></td>
                                            <td class="px-4 py-3 font-bold"><?php echo $sold; ?></td>
                                            <td class="px-4 py-3">
                                                <?php if($p['stock'] <= 0): ?>
                                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Out of Stock</span>
                                                <?php elseif($sold > 0): ?>
                                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">Selling Well</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">No Sales</span>
                                                <?php endif; ?>
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
    </div>
</div>

<?php include 'includes/footer.php'; ?>

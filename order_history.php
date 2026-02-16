<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch orders and cart items
$orders = [];
$cart_items = [];

$uid = intval($_SESSION['user_id']);
$user_col = 'user_id';
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) {
    $user_col = 'customerID';
}

$user_val = $uid;
if ($user_col === 'customerID') {
    if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
        $user_val = intval($_SESSION['customerID']);
    } else {
        $custRes = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . $uid . " LIMIT 1");
        if ($custRes && mysqli_num_rows($custRes) > 0) {
            $crow = mysqli_fetch_assoc($custRes);
            $user_val = intval($crow['customerID']);
            $_SESSION['customerID'] = $user_val;
        }
    }
}

$pstmt = $conn->prepare("SELECT o.order_id,o.product_id,o.quantity,o.price,o.status,o.added_at,p.product_name FROM tbl_orders o LEFT JOIN tbl_products p ON o.product_id = p.product_id WHERE o.$user_col = ? AND o.status = 'purchased' ORDER BY o.added_at DESC");
$pstmt->bind_param('i', $user_val);
$pstmt->execute();
$pres = $pstmt->get_result();
if ($pres) {
    while ($r = $pres->fetch_assoc()) $orders[] = $r;
}

$cstmt = $conn->prepare("SELECT o.order_id,o.product_id,o.quantity,o.price,o.status,o.added_at,p.product_name FROM tbl_orders o LEFT JOIN tbl_products p ON o.product_id = p.product_id WHERE o.$user_col = ? AND o.status = 'active' ORDER BY o.added_at DESC");
$cstmt->bind_param('i', $user_val);
$cstmt->execute();
$cres = $cstmt->get_result();
if ($cres) {
    while ($r = $cres->fetch_assoc()) $cart_items[] = $r;
}

function render_order_history($orders, $cart_items = []) {
    ob_start();
    ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold">Order History</h2>
                <p class="text-sm text-gray-500">All your past purchases and order details.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="search" id="filter" placeholder="Search by order id, product, or date" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <a href="dashboard.php" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Shop</a>
            </div>
        </div>

        <?php if (empty($orders) && empty($cart_items)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v6H3V3zM3 13h18v8H3v-8z"/></svg>
                <h3 class="mt-4 text-lg font-semibold">No orders found</h3>
                <p class="text-sm text-gray-500 mt-2">Looks like you haven't placed any orders yet.</p>
                <a href="index.php" class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php if (!empty($cart_items)): ?>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <h3 class="font-semibold text-gray-700 mb-3">Items Currently in Cart</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <?php foreach ($cart_items as $item):
                                $subtotal = floatval($item['price']) * intval($item['quantity']);
                            ?>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border">
                                <div>
                                    <div class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></div>
                                    <div class="text-xs text-gray-500">Added: <?php echo htmlspecialchars($item['added_at'] ?? $item['created_at'] ?? ''); ?></div>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Status: <?php echo htmlspecialchars($item['status'] ?? 'active'); ?></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">Qty: <?php echo intval($item['quantity']); ?></div>
                                    <div class="font-semibold">₱<?php echo number_format($subtotal,2); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($orders)): ?>
                    <h3 class="text-lg font-bold">Purchased Items</h3>
                    <div class="space-y-4">
                        <?php foreach ($orders as $order): ?>
                            <div class="card-hover transition-all duration-200 bg-white border border-gray-100 rounded-xl p-5">
                                <div class="md:flex md:items-center md:justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                            <i class="fas fa-box-open text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Order</div>
                                            <div class="font-semibold text-gray-800">ID: <?php echo htmlspecialchars($order['order_id'] ?? ($order['id'] ?? $order['order_number'] ?? '—')); ?></div>
                                            <div class="text-xs text-gray-500">Date: <?php echo htmlspecialchars($order['order_date'] ?? $order['date'] ?? '—'); ?></div>
                                        </div>
                                    </div>

                                    <div class="mt-4 md:mt-0 text-right">
                                        <div class="text-sm text-gray-500">Total</div>
                                        <div class="font-semibold text-gray-800">₱<?php echo number_format(floatval($order['total'] ?? $order['amount'] ?? 0), 2); ?></div>
                                        <div class="mt-2">
                                            <a href="#" class="text-sm text-purple-600 hover:underline">View Details</a>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $items = [];
                                if (!empty($order['items'])) {
                                    $decoded = json_decode($order['items'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $items = $decoded;
                                    }
                                }
                                if (empty($items) && isset($order['items']) && is_array($order['items'])) {
                                    $items = $order['items'];
                                }
                                ?>

                                <?php if (!empty($items)): ?>
                                    <div class="mt-4 border-t pt-4">
                                        <div class="text-sm text-gray-500 mb-2">Items</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <?php foreach ($items as $it):
                                                $name = htmlspecialchars($it['name'] ?? $it['product_name'] ?? (is_array($it) && isset($it[0]['name']) ? $it[0]['name'] : 'Item'));
                                                $qty = intval($it['qty'] ?? $it['quantity'] ?? 1);
                                                $price = number_format(floatval($it['price'] ?? $it['amount'] ?? 0), 2);
                                            ?>
                                                <div class="p-3 bg-gray-50 rounded-lg">
                                                    <div class="text-sm font-medium truncate"><?php echo $name; ?></div>
                                                    <div class="text-xs text-gray-500 mt-1">Qty: <?php echo $qty; ?> · ₱<?php echo $price; ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('filter')?.addEventListener('input', function(e){
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.card-hover').forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
        
        function updateOrderStatus(orderId, status) {
            if (!confirm('Are you sure you want to set status to ' + status + '?')) return;
            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: status })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Simple behavior: reload to reflect changes
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Request failed'); });
        }
    </script>
    <?php
    return ob_get_clean();
}

// If this file is included into another page, only output the inner content.
if (basename($_SERVER['PHP_SELF']) !== basename(__FILE__)) {
    echo render_order_history($orders, $cart_items);
    return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Order History</title>
    <style>.card-hover:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); }</style>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">
    <div class="p-6">
        <?php include './components/header-dashboard.php'; ?>
        <?php echo render_order_history($orders, $cart_items); ?>
    </div>
</body>
</html>

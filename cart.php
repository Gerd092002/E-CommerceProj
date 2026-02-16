<?php


include 'connection.php';
if(!isset($_SESSION['user_id'])){
 
    header('Location: index.php');
    
    exit;
}

$cartItems = [];
$total = 0;

// If user is logged in, load cart from tbl_orders; otherwise fallback to session cart
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    // detect column name and resolve value
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
    $sql = "SELECT o.order_id, o.product_id, o.quantity, o.price, p.product_name FROM tbl_orders o LEFT JOIN tbl_products p ON o.product_id = p.product_id WHERE o.$user_col = ? AND o.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_val);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
            while ($r = $res->fetch_assoc()) {
            $pid = intval($r['product_id']);
            $cartItems[$pid] = [
                'qty' => intval($r['quantity']),
                'price' => floatval($r['price']),
                'name' => $r['product_name'] ?? 'Product',
                'order_id' => $r['order_id']
            ];
        }
    }
} else {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $cartItems = $_SESSION['cart'];
}

// mag determine kung asa siya na page mo direct after logged, mag depende sa iya role
$backUrl = isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!--  Font Awesome icons para parehas sa tanan pages -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Shopping Cart - Adidadidadas</title>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Same gradient sa index.php */
        }
        .cart-item-card {
            transition: all 0.3s ease;
        }
        .cart-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    

    <!-- Main Content  gi-improve ang layout ug design -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- gepadak'an ang header -->
            <div class="mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                    Shopping Cart
                </h2>
                <p class="text-gray-600">
                    Review your items and proceed to checkout
                </p>
            </div>

            <?php if (count($cartItems) === 0): ?>
                <!--  Empty cart design -->
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="bg-purple-100 p-6 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shopping-cart text-4xl text-purple-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h3>
                        <p class="text-gray-600 mb-8">Looks like you haven't added any items to your cart yet.</p>
                        <a href="<?php echo $backUrl; ?>" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white px-8 py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-600 hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-store"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- item section ge usab sa ai hahahah -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <i class="fas fa-list text-purple-600"></i>
                                Cart Items (<?php echo count($cartItems); ?>)
                            </h3>
                            
                            <div class="space-y-4">
                                <?php foreach ($cartItems as $product_id => $item): 
                                    $subtotal = $item['price'] * $item['qty'];
                                    $total += $subtotal;
                                ?>
                                <div class="cart-item-card bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                        <!-- Nag add product image placeholder, optional pwede ra tanggalon -->
                                        <div class="bg-white p-4 rounded-lg border border-gray-300 w-20 h-20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box text-2xl text-gray-400"></i>
                                        </div>
                                        
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="text-gray-600 text-sm">Product ID: #<?php echo $product_id; ?></p>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                                            <!-- Price -->
                                            <div class="text-center">
                                                <div class="text-sm text-gray-600">Price</div>
                                                <div class="text-xl font-bold text-purple-600">₱<?php echo number_format($item['price'], 2); ?></div>
                                            </div>
                                            
                                            <!--  Quantity controls -->
                                            <div class="text-center">
                                                <div class="text-sm text-gray-600 mb-2">Quantity</div>
                                                <div class="flex items-center gap-2">
                                                    <button 
                                                        class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 transition flex items-center justify-center"
                                                        onclick="updateQty(<?php echo $product_id; ?>, -1)">
                                                        <i class="fas fa-minus text-gray-700"></i>
                                                    </button>
                                                    <span id="qty-<?php echo $product_id; ?>" class="w-10 text-center font-bold text-lg">
                                                        <?php echo $item['qty']; ?>
                                                    </span>
                                                    <button 
                                                        class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 transition flex items-center justify-center"
                                                        onclick="updateQty(<?php echo $product_id; ?>, 1)">
                                                        <i class="fas fa-plus text-gray-700"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Subtotal -->
                                            <div class="text-center">
                                                <div class="text-sm text-gray-600">Subtotal</div>
                                                <div class="text-xl font-bold text-gray-800">₱<?php echo number_format($subtotal, 2); ?></div>
                                            </div>
                                            
                                            <!-- Remove button -->
                                            <button 
                                                class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition"
                                                onclick="removeFromCart(<?php echo $product_id; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!--  Order Summary - gihimo nga card style -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <i class="fas fa-receipt text-purple-600"></i>
                                Order Summary
                            </h3>
                            
                            <!--  Order details ge utro ang layout -->
                            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-semibold text-gray-800">₱<?php echo number_format($total, 2); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-semibold text-green-600">FREE</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="font-semibold text-gray-800">₱0.00</span>
                                </div>
                            </div>

                            <!-- Total -ge highlight ra -->
                            <div class="flex justify-between items-center mb-8">
                                <span class="text-xl font-bold text-gray-800">Total</span>
                                <span class="text-3xl font-bold text-purple-600">₱<?php echo number_format($total, 2); ?></span>
                            </div>

                            <!--  Checkout button - gradient  -->
                            <a href="checkout.php" 
                               class="block w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-pink-600 hover:shadow-lg transition-all duration-300 text-center mb-4">
                                <i class="fas fa-lock mr-2"></i>
                                Proceed to Checkout
                            </a>

                            <!--  Continue shopping button -->
                            <a href="<?php echo $backUrl; ?>" 
                               class="block w-full text-center border-2 border-purple-600 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition-all duration-300">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Continue Shopping
                            </a>

                            <!-- Nag add ug Payment methods info eme lang gud  -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">We accept:</p>
                                <div class="flex gap-3">
                                    <div class="bg-gray-100 p-2 rounded">
                                        <i class="fab fa-cc-visa text-blue-600"></i>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <i class="fab fa-cc-mastercard text-red-600"></i>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <i class="fas fa-university text-green-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

   

    <!-- na usab JavaScript functions - parehas functionality ge usab sa ai hahahah -->
    <script>
        function updateQty(productId, delta) {
            const qtyElement = document.getElementById('qty-' + productId);
            const currentQty = parseInt(qtyElement.textContent);
            const newQty = Math.max(1, currentQty + delta);
            
            // nag add Visual feedback
            qtyElement.style.transform = 'scale(1.2)';
            setTimeout(() => {
                qtyElement.style.transform = 'scale(1)';
            }, 300);
            
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: newQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qtyElement.textContent = newQty;
                    location.reload(); // Reload to update totals
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update cart');
            });
        }

        function removeFromCart(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Nag add ug Success notification
                        alert('Item removed from cart successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to remove item');
                });
            }
        }
    </script>
</body>
</html>
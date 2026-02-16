<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
$total = 0;

$continueShoppingUrl = isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php';

if (count($cartItems) === 0) {
    header("Location: cart.php");
    exit();
}

foreach ($cartItems as $product_id => $item) {
    $subtotal = $item['price'] * $item['qty'];
    $total += $subtotal;
}

// GI FIX: Naay typo sa variable name - "gitnt_method" instead na "payment_method"
$payment_method = $_POST['payment_method'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($payment_method)) {
        $error = "All fields are required!";
    } else {
        $_SESSION['order_summary'] = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'payment_method' => $payment_method,
            'items' => $cartItems,
            'total' => $total,
            'order_date' => date('Y-m-d H:i:s')
        ];
        unset($_SESSION['cart']);
        
        header("Location: order_confirmation.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome icons para parehas sa tanan pages -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Checkout - Adidadidadas</title>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Same gradient sa index.php */
        }
        .checkout-step {
            transition: all 0.3s ease;
        }
        .checkout-step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!--  Header design - gradient background ug modern layout -->
    <!-- from gray bg to Gradient background with icons -->
    <header class="gradient-bg shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <a href="<?php echo $continueShoppingUrl; ?>" class="text-white hover:text-gray-200 font-medium flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Back to <?php echo isset($_SESSION['user_id']) ? 'Dashboard' : 'Shop'; ?>
                    </a>
                    <div class="flex items-center gap-3">
                        
                        <?php
                        $logo_path = 'uploads/LOGO.png';
                        if (file_exists($logo_path)) {
                            echo '<img src="' . $logo_path . '" alt="Adidadidadas Logo" class="h-12 w-auto rounded-lg border-2 border-white shadow-lg">';
                        }
                        ?>
                        <h1 class="text-3xl font-bold text-white">Adidadidadas</h1>
                    </div>
                </div>
                
                <!-- ge change  Cart ug logout buttons ge parehas design sa index.php -->
                <div class="flex items-center gap-4">
                    <a href="cart.php" class="relative bg-white text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo count($cartItems); ?>
                        </span>
                    </a>
                    
                    <!-- Logout button  -->
                    <a href="logout.php" class="bg-white text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Nag add Checkout steps indicator pwede ra gud tanggalon -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-center">
            <div class="flex items-center gap-2">
                <div class="checkout-step active flex items-center gap-2 px-4 py-2 rounded-full">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="hidden sm:inline">Cart</span>
                </div>
                <div class="w-8 h-1 bg-purple-300"></div>
                <div class="checkout-step bg-white text-purple-600 border-2 border-purple-600 flex items-center gap-2 px-4 py-2 rounded-full font-bold">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="hidden sm:inline">Checkout</span>
                </div>
                <div class="w-8 h-1 bg-gray-300"></div>
                <div class="checkout-step bg-gray-100 text-gray-500 flex items-center gap-2 px-4 py-2 rounded-full">
                    <i class="fas fa-check-circle"></i>
                    <span class="hidden sm:inline">Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ge change ang Main Content  -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- ge change nag Page header -->
            <div class="mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <i class="fas fa-clipboard-check text-purple-600"></i>
                    Checkout
                </h2>
                <p class="text-gray-600">
                    Complete your order by providing shipping and payment information
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- ge change ang Checkout Form  -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-truck text-purple-600"></i>
                            Shipping & Payment Information
                        </h3>

                        <?php if (isset($error)): ?>
                            <!-- ge change ang design sa Error message -->
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-1"></i>
                                <div>
                                    <p class="font-semibold">Please check the following:</p>
                                    <p><?php echo $error; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <!-- ge change ang Form fields  -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                        <i class="fas fa-user text-purple-600"></i>
                                        Full Name
                                    </label>
                                    <input type="text" name="full_name" required 
                                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                                           placeholder="Enter your full name">
                                    <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 mt-8"></i>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                        <i class="fas fa-envelope text-purple-600"></i>
                                        Email Address
                                    </label>
                                    <input type="email" name="email" required 
                                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                                           placeholder="your@email.com">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 mt-8"></i>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                        <i class="fas fa-phone text-purple-600"></i>
                                        Phone Number
                                    </label>
                                    <input type="tel" name="phone" required 
                                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                                           placeholder="0912 345 6789">
                                    <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 mt-8"></i>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                        <i class="fas fa-city text-purple-600"></i>
                                        City
                                    </label>
                                    <input type="text" name="city" required 
                                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                                           placeholder="Enter your city">
                                    <i class="fas fa-city absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 mt-8"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                    <i class="fas fa-home text-purple-600"></i>
                                    Complete Address
                                </label>
                                <textarea name="address" required rows="3" 
                                          class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300 resize-none"
                                          placeholder="House/Building No., Street, Barangay"></textarea>
                                <i class="fas fa-home absolute left-4 top-8 transform text-gray-400"></i>
                            </div>

                            <!-- ge change ang Payment method naka dropdown design -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">
                                    <i class="fas fa-credit-card text-purple-600"></i>
                                    Payment Method
                                </label>
                                <div class="relative">
                                    <select name="payment_method" required 
                                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300 appearance-none">
                                        <option value="">Select payment method</option>
                                        <option value="Credit Card">💳 Credit Card</option>
                                        <option value="Debit Card">🏦 Debit Card</option>
                                        <option value="Bank Transfer">🏛️ Bank Transfer</option>
                                        <option value="Cash on Delivery">💰 Cash on Delivery</option>
                                        <option value="E-Wallet">📱 E-Wallet (GCash/PayMaya)</option>
                                    </select>
                                    <i class="fas fa-wallet absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- ge change ang design sa  Action buttons  -->
                            <div class="space-y-4">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-pink-600 hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-3">
                                    <i class="fas fa-lock"></i>
                                    Place Order & Pay Now
                                </button>
                                
                                <a href="<?php echo $continueShoppingUrl; ?>" 
                                   class="block w-full text-center border-2 border-purple-600 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition-all duration-300">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Continue Shopping
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ge change ang design sa Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-receipt text-purple-600"></i>
                            Order Summary
                        </h3>
                        
                        <!--  Order items -->
                        <div class="space-y-4 mb-6 pb-6 border-b border-gray-200 max-h-64 overflow-y-auto">
                            <?php foreach ($cartItems as $product_id => $item): 
                                $subtotal = $item['price'] * $item['qty'];
                            ?>
                                <div class="flex items-start justify-between bg-gray-50 p-3 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="text-sm text-gray-600">Qty: <?php echo $item['qty']; ?></div>
                                    </div>
                                    <div class="font-bold text-purple-600">₱<?php echo number_format($subtotal, 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- ge change ang design sa Price breakdown -->
                        <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
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

                        <!-- ge highlight ang  Total amount -->
                        <div class="flex justify-between items-center mb-8">
                            <span class="text-xl font-bold text-gray-800">Total Amount</span>
                            <span class="text-3xl font-bold text-purple-600">₱<?php echo number_format($total, 2); ?></span>
                        </div>

                        <!-- nag add ug Security badge design lang-->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                            <i class="fas fa-shield-alt text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-green-700 font-medium">Secure checkout </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- nag add ug Footer  -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; 2025 Adidadidadas. Premium footwear and apparel.</p>
                <div class="flex justify-center gap-6 mt-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
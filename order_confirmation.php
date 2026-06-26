<?php
session_start();


if (!isset($_SESSION['order_summary'])) {
    header("Location: index.php");
    exit();
}

// Get order details from session
$order = $_SESSION['order_summary'];

$continueShoppingUrl = isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Order Confirmation</title>
</head>
<body>
    <div class="min-h-screen bg-gray-100">

        <div class="bg-gray-200 p-4">
            <div class="flex items-center justify-center gap-4">
                <h1 class="text-2xl sm:text-4xl font-bold">Adidadidadas</h1>
            </div>
        </div>

    
        <div class="max-w-2xl mx-auto p-3 sm:p-4 mt-6 sm:mt-10">
            <div class="bg-white rounded-lg shadow p-4 sm:p-8 text-center">
       
                <div class="mb-4 sm:mb-6">
                    <svg class="w-16 sm:w-20 h-16 sm:h-20 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold text-green-600 mb-2">Order Confirmed!</h2>
                <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">Thank you for your purchase</p>

                <!-- Order Details -->
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 text-left mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-bold mb-4">Order Details</h3>
                    
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6 pb-4 sm:pb-6 border-b border-gray-200 text-xs sm:text-sm">
                        <p><span class="font-semibold">Order Date:</span> <span class="break-all"><?php echo $order['order_date']; ?></span></p>
                        <p><span class="font-semibold">Name:</span> <span class="break-all"><?php echo htmlspecialchars($order['full_name']); ?></span></p>
                        <p><span class="font-semibold">Email:</span> <span class="break-all"><?php echo htmlspecialchars($order['email']); ?></span></p>
                        <p><span class="font-semibold">Phone:</span> <span class="break-all"><?php echo htmlspecialchars($order['phone']); ?></span></p>
                        <p><span class="font-semibold">Address:</span> <span class="break-all"><?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?></span></p>
                        <p><span class="font-semibold">Payment Method:</span> <span class="break-all"><?php echo htmlspecialchars($order['payment_method']); ?></span></p>
                    </div>

                    <!-- Items -->
                    <h4 class="font-bold mb-3 text-sm sm:text-base">Items Ordered:</h4>
                    <div class="space-y-2 mb-4 text-xs sm:text-sm">
                        <?php foreach ($order['items'] as $product_id => $item): 
                            $subtotal = $item['price'] * $item['qty'];
                        ?>
                            <div class="flex justify-between">
                                <span class="truncate"><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['qty']; ?></span>
                                <span class="font-semibold ml-2">₱<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex justify-between font-bold text-base sm:text-lg pt-4 border-t border-gray-200">
                        <span>Total Amount:</span>
                        <span>₱<?php echo number_format($order['total'], 2); ?></span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="<?php echo $continueShoppingUrl; ?>" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition font-semibold text-base">
                        Continue Shopping
                    </a>
                    <a href="logout.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition font-semibold text-base">
                        Logout
                    </a>
                </div>
                <p class="text-gray-600 mt-4 sm:mt-6 text-xs sm:text-sm break-all">A confirmation email has been sent to <?php echo htmlspecialchars($order['email']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>

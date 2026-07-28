<?php
    include 'connection.php';
    session_start();

    $category_filter = $_GET['category'] ?? '';
    $categoryIdFilter = intval($_GET['category_id'] ?? 0);
    $product_filter = $_GET['filter'] ?? '';
    $search_query = trim($_GET['search'] ?? '');
    $sectionTitle = 'Featured Products';
    $sectionSubtitle = 'Discover our most popular items';

    if ($search_query !== '') {
        $sectionTitle = 'Search Results';
        $sectionSubtitle = 'Showing products that match "' . $search_query . '"';
    } elseif ($category_filter === 'sneakers') {
        $sectionTitle = 'Sneakers';
        $sectionSubtitle = 'Browse our latest shoe collection';
    } elseif ($category_filter === 'apparel') {
        $sectionTitle = 'Apparel';
        $sectionSubtitle = 'Shop shirts, caps, and wearable favorites';
    } elseif ($product_filter === 'new') {
        $sectionTitle = 'New Releases';
        $sectionSubtitle = 'Fresh arrivals from the newest product listings';
    } elseif ($product_filter === 'sales') {
        $sectionTitle = 'Sales';
        $sectionSubtitle = 'Great-value items currently available';
    }

    $cartCount = 0;
    if (isset($_SESSION['user_id'])) {
        $cartUserId = intval($_SESSION['user_id']);
        $cartUserColumn = 'user_id';
        $cartUserValue = $cartUserId;
        $orderColumnCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");

        if ($orderColumnCheck && mysqli_num_rows($orderColumnCheck) > 0) {
            $cartUserColumn = 'customerID';

            if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
                $cartUserValue = intval($_SESSION['customerID']);
            } else {
                $customerResult = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . $cartUserId . " LIMIT 1");
                if ($customerResult && mysqli_num_rows($customerResult) > 0) {
                    $customerRow = mysqli_fetch_assoc($customerResult);
                    $cartUserValue = intval($customerRow['customerID']);
                    $_SESSION['customerID'] = $cartUserValue;
                }
            }
        }

        $countSql = "SELECT COUNT(*) AS total FROM tbl_orders WHERE $cartUserColumn = ? AND status = 'active'";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param('i', $cartUserValue);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        if ($countResult) {
            $countRow = $countResult->fetch_assoc();
            $cartCount = intval($countRow['total'] ?? 0);
        }
        $countStmt->close();
    }

    $totalProducts = 0;
    $totalProductsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_products");
    if ($totalProductsResult) {
        $totalProductsRow = mysqli_fetch_assoc($totalProductsResult);
        $totalProducts = intval($totalProductsRow['total'] ?? 0);
    }

    $shopCategories = [];
    $categoryResult = mysqli_query($conn, "SELECT category_id, categoryName FROM tbl_categories ORDER BY categoryName ASC");
    if ($categoryResult) {
        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
            $shopCategories[] = $categoryRow;
        }
    }

    $shopProducts = [];
    $productSql = "
        SELECT
            p.product_id,
            p.product_name,
            p.price,
            p.quantity,
            p.image_path,
            c.categoryName
        FROM tbl_products p
        LEFT JOIN tbl_categories c ON p.category_id = c.category_id
    ";
    $whereClauses = [];
    $bindTypes = '';
    $bindValues = [];

    if ($search_query !== '') {
        $searchValue = '%' . $search_query . '%';
        $whereClauses[] = "(p.product_name LIKE ? OR c.categoryName LIKE ? OR CAST(p.price AS CHAR) LIKE ?)";
        $bindTypes .= 'sss';
        $bindValues[] = $searchValue;
        $bindValues[] = $searchValue;
        $bindValues[] = $searchValue;
    }

    if ($categoryIdFilter > 0) {
        $whereClauses[] = "p.category_id = ?";
        $bindTypes .= 'i';
        $bindValues[] = $categoryIdFilter;
    } elseif ($category_filter === 'sneakers') {
        $whereClauses[] = "c.categoryName LIKE '%Shoes%'";
    } elseif ($category_filter === 'apparel') {
        $whereClauses[] = "(c.categoryName LIKE '%Shirt%' OR c.categoryName LIKE '%Cap%')";
    }

    if ($product_filter === 'sales') {
        $whereClauses[] = "p.price <= 1000";
    }

    if (!empty($whereClauses)) {
        $productSql .= " WHERE " . implode(" AND ", $whereClauses);
    }
    $productSql .= " ORDER BY p.product_id DESC";

    $productStmt = $conn->prepare($productSql);
    if ($productStmt) {
        if ($bindTypes !== '') {
            $productStmt->bind_param($bindTypes, ...$bindValues);
        }
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        if ($productResult) {
            while ($productRow = $productResult->fetch_assoc()) {
                $shopProducts[] = $productRow;
            }
        }
        $productStmt->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!--  Font Awesome para sa mga icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Adidadidadas - Premium Online Store</title>
    <!-- Nag add ug Custom styles for better visual appeal -->
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px); /* Mu-lift ang product kung i-hover  */
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Gradient background */
        }
        .category-btn {
            transition: all 0.3s ease;
        }
        .category-btn:hover {
            background-color: #4f46e5;
            transform: scale(1.05); /* Mu-dako gamay ang button kung i-hover  */
        }
    </style>
</head>
<body class="bg-gray-50 ">
    <!-- Ge Modified ang Header   -->
    <header id="home" class="gradient-bg shadow-lg ">
        <div class="container mx-auto px-4 py-6">
            <div class="flex  md:flex-row justify-between mr-10 ml-6 items-center gap-6">
                
                <div class="hidden lg:block">
                    
                    <div class="flex items-center gap-3">
                        <?php
                        $logo_path = 'uploads/LOGO.png';
                        if (file_exists($logo_path)) {
                            echo '<img src="' . $logo_path . '" alt="Adidadidadas Logo" class="h-12 w-auto rounded-lg border-2 border-white shadow-lg">';
                        }
                        ?>
                        <h1 class="text-3xl  md:text-4xl font-bold text-white">Adidadidadas</h1>
                    </div>
                </div>
                <!-- mobile view menu bar -->
                 <div class="block md:hidden w-10 flex justify-end ">
                        <button class="text-white btn-menu">
                           <i class="fa-solid fa-bars text-4xl "></i>
                        </button>
                </div>
                
                <!-- ge change ang mga buttons nag add  og icons ug cart badge -->
                 <!-- desktop view -->
                <div class=" hidden lg:block ">
                   <div class="flex">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                       
                        
                        <a href="cart.php" class=" bg-white text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-shopping-cart"></i>
                            Cart
                            <span class="cart-count-badge bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cartCount; ?></span>
                        </a>
                    <?php endif ; ?>
                     <?php if(isset($_SESSION['user_id'])) :?>
                        <a href="dashboard.php?content=profile" class="bg-white ml-12 text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                    <?php else : ?>
                         <a href="login.php" class="bg-white ml-12 text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                        <i class="fas fa-user"></i>
                        Login/Create
                    </a>
                    <?php endif ;?>
                    </div>
                </div>
               
            </div>
            <!-- mobile view  -->
            <div class="flex flex-col gap-4 mt-4 hidden mobilebox transition-opacity duration-500 delay-200 ease-in-out">
               <?php if(isset($_SESSION['user_id'])) : ?>
                       
                        
                        <a href="cart.php" class=" bg-white text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-shopping-cart"></i>
                            Cart
                            <span class="cart-count-badge bg-red-500 text-white text-xs rounded-xl h-5 w-5 flex items-center justify-center"><?php echo $cartCount; ?></span>
                        </a>
                    <?php endif ; ?>
                     <?php if(isset($_SESSION['user_id'])) :?>
                        <a href="dashboard.php?content=profile" class="bg-white  text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                    <?php else : ?>
                         <a href="login.php" class="bg-white  text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                        <i class="fas fa-user"></i>
                        Login/Create
                    </a>
                    <?php endif ;?>
            </div>

        </div>
    </header>

    <main id="products" class="container mx-auto px-4 py-8 scroll-mt-24">
        <section class="space-y-8">
            <div class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-700 rounded-3xl shadow-lg p-8 text-white overflow-hidden relative">
                <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
                <div class="absolute right-20 -bottom-20 h-44 w-44 rounded-full bg-white/10"></div>
                <div class="relative flex flex-col xl:flex-row xl:items-center xl:justify-between gap-8">
                    <div class="max-w-2xl">
                        <p class="text-sm uppercase tracking-[0.25em] text-orange-100 mb-3">Adidadidadas Mall</p>
                        <h2 class="text-5xl font-black mb-3 leading-tight">Shop faster, cleaner, better.</h2>
                        <p class="text-orange-50 text-lg">A Shopee-style shopping page with product discovery, smart search, cart actions, and order access in one place.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 min-w-[320px]">
                        <div class="bg-white/15 backdrop-blur rounded-2xl px-5 py-4 border border-white/20">
                            <p class="text-xs uppercase tracking-[0.2em] text-orange-100">Products</p>
                            <div class="text-4xl font-bold"><?php echo number_format($totalProducts); ?></div>
                        </div>
                        <a href="cart.php" class="bg-white text-purple-700 rounded-2xl px-5 py-4 shadow-lg hover:scale-[1.02] transition">
                            <p class="text-xs uppercase tracking-[0.2em] text-purple-400">Cart Items</p>
                            <div class="text-4xl font-bold cart-count-badge"><?php echo number_format($cartCount); ?></div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center"><i class="fas fa-truck-fast"></i></div>
                    <div><h3 class="font-bold text-gray-800">Fast Checkout</h3><p class="text-sm text-gray-500">Cart is always one click away.</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fas fa-magnifying-glass"></i></div>
                    <div><h3 class="font-bold text-gray-800">Smart Search</h3><p class="text-sm text-gray-500">Search by Cateory or Name.</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center"><i class="fas fa-receipt"></i></div>
                    <div><h3 class="font-bold text-gray-800">Order Tracking</h3><p class="text-sm text-gray-500">History is available from the top menu.</p></div>
                </div>
            </div>

            <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($sectionTitle); ?></h3>
                        <p class="text-gray-500"><?php echo htmlspecialchars($sectionSubtitle); ?></p>
                    </div>
                    <form method="GET" action="index.php#products" class="relative w-full xl:max-w-xl">
                        <input
                            type="search"
                            name="search"
                            value="<?php echo htmlspecialchars($search_query); ?>"
                            placeholder="Search product name or category..."
                            class="w-full pl-12 pr-28 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50"
                        >
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 text-white px-5 py-2.5 rounded-xl hover:bg-orange-600 transition">
                            Search
                        </button>
                    </form>
                </div>

                <div class="flex gap-3 overflow-x-auto pb-4 mb-4">
                    <a href="index.php#products" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($categoryIdFilter === 0 && $category_filter === '' && $product_filter === '' && $search_query === '') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                        <i class="fas fa-border-all mr-2"></i>All
                    </a>
                    <a href="index.php?filter=sales#products" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($product_filter === 'sales') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                        <i class="fas fa-tags mr-2"></i>Deals
                    </a>
                    <?php foreach ($shopCategories as $category): ?>
                        <a href="index.php?category_id=<?php echo intval($category['category_id']); ?>#products" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($categoryIdFilter === intval($category['category_id'])) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                            <?php echo htmlspecialchars($category['categoryName']); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($search_query !== ''): ?>
                        <a href="index.php#products" class="shrink-0 px-5 py-3 rounded-2xl font-semibold bg-red-50 text-red-600 hover:bg-red-100">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($shopProducts)): ?>
                    <div class="text-center py-16 border border-dashed border-gray-200 rounded-3xl">
                        <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700">No products found</h3>
                        <p class="text-gray-500 mt-2">Try another search or category.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                        <?php foreach ($shopProducts as $product): ?>
                            <?php
                                $productId = intval($product['product_id']);
                                $productName = htmlspecialchars($product['product_name'] ?? 'Product');
                                $categoryName = htmlspecialchars($product['categoryName'] ?? 'Uncategorized');
                                $rawImagePath = trim($product['image_path'] ?? '');
                                $defaultImage = 'uploads/LOGO.png';
                                $imagePath = $defaultImage;
                                if ($rawImagePath !== '') {
                                    $candidatePath = $rawImagePath;
                                    $fileCheckPath = $candidatePath;
                                    if (!file_exists($fileCheckPath)) {
                                        $fileCheckPath = __DIR__ . '/' . $candidatePath;
                                    }
                                    if (file_exists($fileCheckPath)) {
                                        $imagePath = $candidatePath;
                                    }
                                }
                                $imagePath = htmlspecialchars($imagePath);
                                $price = floatval($product['price'] ?? 0);
                                $quantity = intval($product['quantity'] ?? 0);
                                $isLowStock = $quantity > 0 && $quantity <= 10;
                            ?>
                            <article class="group bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">
                                <div class="relative h-56 bg-gray-100 overflow-hidden">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $productName; ?>" class="h-full w-full object-cover group-hover:scale-105 transition duration-500">
                                    <span class="absolute left-3 top-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">Mall</span>
                                    <?php if ($isLowStock): ?>
                                        <span class="absolute right-3 top-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">Low stock</span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-5">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2"><?php echo $categoryName; ?></p>
                                    <h4 class="font-bold text-gray-900 line-clamp-2 min-h-[3rem]"><?php echo $productName; ?></h4>
                                    <div class="mt-4 flex items-end justify-between gap-3">
                                        <div>
                                            <p class="text-xs text-gray-400">Price</p>
                                            <p class="text-2xl font-black text-orange-500">&#8369;<?php echo number_format($price, 2); ?></p>
                                        </div>
                                        <p class="text-xs text-gray-500"><?php echo number_format($quantity); ?> left</p>
                                    </div>
                                    <button
                                        type="button"
                                        onclick="addToCart(<?php echo $productId; ?>, this)"
                                        <?php echo ($quantity <= 0) ? 'disabled' : ''; ?>
                                        class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 font-bold transition <?php echo ($quantity <= 0) ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-purple-700 text-white hover:bg-purple-800'; ?>"
                                    >
                                        <i class="fas fa-cart-plus"></i>
                                        <?php echo ($quantity <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    </main>

    <!-- nag add ug Footer  -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">Adidadidadas</h4>
                    <p class="text-gray-400">Premium footwear and apparel for the modern lifestyle.</p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Quick Links</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Shipping Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Returns</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Support</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Connect With Us</h5>
                    <div class="flex gap-4">
                        <a href="#" class="bg-gray-800 p-3 rounded-full hover:bg-purple-600 transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="bg-gray-800 p-3 rounded-full hover:bg-purple-600 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="bg-gray-800 p-3 rounded-full hover:bg-purple-600 transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Adidadidadas. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>

            const btnmenu = document.querySelector('.btn-menu');
            const mobilebox = document.querySelector('.mobilebox');
            btnmenu.addEventListener('click',(e)=>{

                e.preventDefault();
                mobilebox.classList.toggle('opacity-0');
                mobilebox.classList.toggle('hidden');
                mobilebox.classList.toggle('opacity-100')
            })




        function addToCart(productId, button) {
            // Use passed button element when available (sa inline onclick we pass `this`)
            if (!button) {
                button = (typeof event !== 'undefined' && event.target) ? event.target : null;
            }
            const originalText = button ? button.innerHTML : 'Adding...';
            if (button) {
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                button.disabled = true;
            }
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: 1
                })
            })
            .then(response => response.text())
            .then(text => {
                // Try to parse JSON; if parsing fails, log the full response for debugging
                let data = null;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    console.error('Non-JSON response from add_to_cart.php:', text);
                    showNotification('Server error while adding to cart (see console)', 'error');
                    if (button) {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                    return;
                }

                if (data && data.success) {
                    showNotification('Product added to cart!', 'success');
                    document.querySelectorAll('.cart-count-badge').forEach((cartBadge) => {
                        cartBadge.textContent = data.cart_count ?? cartBadge.textContent;
                    });
                } else {
                    console.error('add_to_cart error response:', data);
                    showNotification('Error: ' + (data.error || 'Unknown error'), 'error');
                }

                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Fetch error adding to cart:', error);
                showNotification('Network error while adding to cart', 'error');
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });
        }
        
        //  Notification function 
        function showNotification(message, type) {
            // Mug-himo og notification element 
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Mawa human sa 3 seconds 
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>

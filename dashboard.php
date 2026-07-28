<?php
    ob_start();
    session_start();
    include 'connection.php';

    // 1. Security Check
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    // 2. Pag-ihap sa tanang produkto (Dynamic)
    $query = "SELECT COUNT(*) as total FROM tbl_products"; 
    $result = mysqli_query($conn, $query);
    $totalProducts = 0;
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalProducts = $row['total'];
    }

    $userType = isset($_SESSION['userType']) ? strtolower($_SESSION['userType']) : 'customer';
    $activeContent = $_GET['content'] ?? '';
    $search_query = trim($_GET['search'] ?? '');
    $category_filter = $_GET['category'] ?? '';
    $categoryIdFilter = intval($_GET['category_id'] ?? 0);
    $product_filter = $_GET['filter'] ?? '';

    // Fetch user profile picture
    $userId = intval($_SESSION['user_id']);
    $userProfileQuery = mysqli_query($conn, "SELECT profile_picture FROM tbl_user WHERE user_id = $userId LIMIT 1");
    $userProfile = ($userProfileQuery && mysqli_num_rows($userProfileQuery) > 0) ? mysqli_fetch_assoc($userProfileQuery) : [];
    $profilePicture = !empty($userProfile['profile_picture']) && file_exists($userProfile['profile_picture']) ? $userProfile['profile_picture'] : null;

    $cartCount = 0;
    $cartUserColumn = 'user_id';
    $cartUserValue = intval($_SESSION['user_id']);
    $orderColumnCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");

    if ($orderColumnCheck && mysqli_num_rows($orderColumnCheck) > 0) {
        $cartUserColumn = 'customerID';

        if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
            $cartUserValue = intval($_SESSION['customerID']);
        } else {
            $customerResult = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . intval($_SESSION['user_id']) . " LIMIT 1");
            if ($customerResult && mysqli_num_rows($customerResult) > 0) {
                $customerRow = mysqli_fetch_assoc($customerResult);
                $cartUserValue = intval($customerRow['customerID']);
                $_SESSION['customerID'] = $cartUserValue;
            }
        }
    }

    // Count cart items. Support both new ('active') and legacy ('Logged In') status values.
    $cartCountSql = "SELECT COUNT(*) AS total FROM tbl_orders WHERE $cartUserColumn = ? AND (status = 'active' OR status = 'Logged In')";
    $cartCountStmt = $conn->prepare($cartCountSql);
    if ($cartCountStmt) {
        $cartCountStmt->bind_param('i', $cartUserValue);
        $cartCountStmt->execute();
        $cartCountResult = $cartCountStmt->get_result();
        if ($cartCountResult) {
            $cartCountRow = $cartCountResult->fetch_assoc();
            $cartCount = intval($cartCountRow['total'] ?? 0);
        }
        $cartCountStmt->close();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Dashboard - Adidadidadas</title>
    <style>
        .sidebar-btn { transition: all 0.3s ease; }
        .sidebar-btn:hover { transform: translateX(5px); }
        body { overflow: hidden; }
        #main-content::-webkit-scrollbar { width: 6px; }
        #main-content::-webkit-scrollbar-track { background: #f1f1f1; }
        #main-content::-webkit-scrollbar-thumb { background: #d8b4fe; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-100">
    
    <div class="flex h-screen overflow-hidden"> 
        
        <div class="w-64 bg-gradient-to-b from-purple-800 to-purple-900 text-white flex flex-col fixed h-screen left-0 top-0 z-50">
            <div class="p-6 flex-grow overflow-y-auto">
                <div class="flex flex-col items-center gap-3 mb-10 pb-4 border-b border-purple-700">
                    <div class="h-16 w-16 rounded-full bg-purple-600 flex items-center justify-center border-2 border-white shadow-lg overflow-hidden flex-shrink-0">
                        <?php if ($profilePicture): ?>
                            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile" class="h-full w-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-user text-2xl text-white"></i>
                        <?php endif; ?>
                    </div>
                    <div class="text-center">
                        <h1 class="text-lg font-bold">
                            <?php
                                if (isset($_SESSION['username'])) {
                                    echo htmlspecialchars($_SESSION['username']);
                                } else {
                                    echo 'User';
                                }
                            ?>
                        </h1>
                        <p class="text-xs text-purple-300">
                            <?php
                                if (isset($_SESSION['userType'])) {
                                    echo ucfirst(htmlspecialchars($_SESSION['userType']));
                                } else {
                                    echo 'Customer';
                                }
                            ?>
                        </p>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="?content=profile" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo ($activeContent === 'profile') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                        <i class="fas fa-user text-lg"></i>
                        <span>My Profile</span>
                    </a>

                    <a href="?content=cart" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo ($activeContent === 'cart') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span>Cart</span>
                        <span id="dashboard-cart-count" class="ml-auto bg-white text-purple-700 text-xs font-bold rounded-full min-w-6 h-6 px-2 flex items-center justify-center"><?php echo $cartCount; ?></span>
                    </a>

                    <a href="?content=history" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo ($activeContent === 'history') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                        <i class="fas fa-receipt text-lg"></i>
                        <span>Order History</span>
                    </a>

                    <?php if ($userType === 'admin' || $userType === 'staff') : ?>
                        <div class="pt-4 pb-1 text-xs text-purple-300 uppercase font-semibold px-4 tracking-wider">Management</div>
                        
                        <a href="?content=add_product" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (isset($_GET['content']) && $_GET['content'] == 'add_product') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                            <i class="fas fa-plus-circle text-lg"></i>
                            <span>Add Product</span>
                        </a>
                        <a href="?content=manage_product" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (isset($_GET['content']) && $_GET['content'] == 'manage_product') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                            <i class="fas fa-tasks text-lg"></i>
                            <span>Manage Product</span>
                        </a>
                        <a href="?content=add_category" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (isset($_GET['content']) && $_GET['content'] == 'add_category') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                            <i class="fas fa-plus-circle text-lg"></i>
                            <span>Add Category</span>
                        </a>
                        <a href="?content=manage_categories" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (isset($_GET['content']) && $_GET['content'] == 'manage_categories') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                            <i class="fas fa-tags text-lg"></i>
                            <span>Manage Categories</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($userType === 'admin') : ?>
                        <div class="pt-4 pb-1 text-xs text-purple-300 uppercase font-semibold px-4 tracking-wider">System</div>
                        <a href="?content=user_log" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (isset($_GET['content']) && $_GET['content'] == 'user_log') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                            <i class="fa-solid fa-user-clock text-lg"></i>
                            <span>User Log</span>
                        </a>
                    <?php endif; ?>

                    <a href="?content=settings" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg mt-4 <?php echo (isset($_GET['content']) && $_GET['content'] == 'settings') ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                        <i class="fas fa-cog text-lg"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            <div class="p-6 border-t border-purple-800">
                <a href="logout.php" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-600/20 text-purple-200">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <div id="main-content" class="flex-1 ml-64 h-screen overflow-y-auto bg-gray-50 flex flex-col">
            <div class="p-10 w-full min-h-screen"> 
                
                <div id="contentArea" class="w-full">
                    <?php
                    if(isset($_GET['content'])){
                        $content = $_GET['content'];
                        $pages = [
                            'profile'           => './profile.php',
                            'manage_categories' => './manage_categories.php',
                            'add_product'       => './add_product.php',
                            'add_category'      => './add_category.php',
                            'manage_product'    => './manage_product.php',
                            'user_log'          => './user_log.php',
                            'edit_log'          => './edit_log.php',
                            'edit_product'      => './edit_product.php',
                            'edit_category'     => './edit_category.php',
                            'settings'          => './settings.php',
                            'history'           => './order_history.php',
                            'cart'              => './cart.php'
                        ];

                        if(array_key_exists($content, $pages) && file_exists($pages[$content])) {
                            include $pages[$content];
                        } else {
                            echo "<div class='p-12 bg-white rounded-3xl border border-dashed border-gray-200 text-center text-gray-400 font-bold'>
                                    <i class='fas fa-search mb-4 text-4xl block'></i> PAGE NOT FOUND
                                  </div>";
                        }
                    } else {
                        ?>
                        <section class="space-y-8">
                            <div class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-700 rounded-3xl shadow-lg p-8 text-white overflow-hidden relative">
                                <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
                                <div class="absolute right-20 -bottom-20 h-44 w-44 rounded-full bg-white/10"></div>
                                <div class="relative flex flex-col xl:flex-row xl:items-center xl:justify-between gap-8">
                                    <div class="max-w-2xl">
                                        <p class="text-sm uppercase tracking-[0.25em] text-orange-100 mb-3">Adidadidadas Mall</p>
                                        <h2 class="text-5xl font-black mb-3 leading-tight">Shop faster, cleaner, better.</h2>
                                        <p class="text-orange-50 text-lg">A Shopee-style customer dashboard with product discovery, search, cart actions, and order access in one place.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 min-w-[320px]">
                                        <div class="bg-white/15 backdrop-blur rounded-2xl px-5 py-4 border border-white/20">
                                            <p class="text-xs uppercase tracking-[0.2em] text-orange-100">Products</p>
                                            <div class="text-4xl font-bold"><?php echo number_format($totalProducts); ?></div>
                                        </div>
                                        <a href="dashboard.php?content=cart" class="bg-white text-purple-700 rounded-2xl px-5 py-4 shadow-lg hover:scale-[1.02] transition">
                                            <p class="text-xs uppercase tracking-[0.2em] text-purple-400">Cart Items</p>
                                            <div class="text-4xl font-bold"><?php echo number_format($cartCount); ?></div>
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
                                    <div><h3 class="font-bold text-gray-800">Smart Search</h3><p class="text-sm text-gray-500">Search by name, category, or price.</p></div>
                                </div>
                                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center"><i class="fas fa-receipt"></i></div>
                                    <div><h3 class="font-bold text-gray-800">Order Tracking</h3><p class="text-sm text-gray-500">History stays in the sidebar.</p></div>
                                </div>
                            </div>

                            <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">Recommended Products</h3>
                                        <p class="text-gray-500">Browse like a marketplace, add like a cart app.</p>
                                    </div>
                                    <form method="GET" action="dashboard.php" class="relative w-full xl:max-w-xl">
                                        <input
                                            type="search"
                                            name="search"
                                            value="<?php echo htmlspecialchars($search_query); ?>"
                                            placeholder="Search products, categories, or prices..."
                                            class="w-full pl-12 pr-28 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50"
                                        >
                                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 text-white px-5 py-2.5 rounded-xl hover:bg-orange-600 transition">
                                            Search
                                        </button>
                                    </form>
                                </div>

                                <div class="flex gap-3 overflow-x-auto pb-4 mb-4">
                                    <a href="dashboard.php" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($categoryIdFilter === 0 && $category_filter === '' && $product_filter === '') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                                        <i class="fas fa-border-all mr-2"></i>All
                                    </a>
                                    <a href="dashboard.php?filter=sales" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($product_filter === 'sales') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                                        <i class="fas fa-tags mr-2"></i>Deals
                                    </a>
                                    <?php foreach ($shopCategories as $category): ?>
                                        <a href="dashboard.php?category_id=<?php echo intval($category['category_id']); ?>" class="shrink-0 px-5 py-3 rounded-2xl font-semibold <?php echo ($categoryIdFilter === intval($category['category_id'])) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-orange-50'; ?>">
                                            <?php echo htmlspecialchars($category['categoryName']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <?php if ($search_query !== ''): ?>
                                        <a href="dashboard.php" class="shrink-0 px-5 py-3 rounded-2xl font-semibold bg-red-50 text-red-600 hover:bg-red-100">
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
                                                $productId   = intval($product['product_id']);
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
                                                $price       = floatval($product['price'] ?? 0);
                                                $quantity    = intval($product['quantity'] ?? 0);
                                                $isLowStock  = $quantity > 0 && $quantity <= 10;
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
                        <?php
                    }
                    ?>
                </div>
                    
                <footer class="mt-20 py-10 text-center border-t border-gray-200">
                    <p class="text-gray-400 text-[10px] uppercase tracking-widest">&copy; 2025 Adidadidadas | Admin System</p>
                </footer>
            </div>
        </div>
    </div>
    <script>
        function addToCart(productId, button) {
            const originalHtml = button ? button.innerHTML : '';
            if (button) {
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
            }

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, qty: 1 })
            })
            .then(response => response.text())
            .then(text => {
                let data = null;
                try { data = JSON.parse(text); } catch (error) {
                    showNotification('Server error while adding to cart', 'error');
                    return;
                }
                if (data.success) {
                    showNotification('Product added to cart!', 'success');
                    const cartBadge = document.getElementById('dashboard-cart-count');
                    if (cartBadge && data.cart_count !== undefined) {
                        cartBadge.textContent = data.cart_count;
                    }
                } else {
                    showNotification(data.error || 'Failed to add product', 'error');
                }
            })
            .catch(() => showNotification('Network error while adding to cart', 'error'))
            .finally(() => {
                if (button) {
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-5 py-3 rounded-xl shadow-lg z-[9999] text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    </script>
</body>
</html>
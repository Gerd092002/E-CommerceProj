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
        /* Custom Scrollbar para sa Content Area */
        #main-content::-webkit-scrollbar { width: 6px; }
        #main-content::-webkit-scrollbar-track { background: #f1f1f1; }
        #main-content::-webkit-scrollbar-thumb { background: #d8b4fe; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-100">
    
    <div class="flex h-screen overflow-hidden"> 
        
        <div class="w-64 bg-gradient-to-b from-purple-800 to-purple-900 text-white flex flex-col fixed h-screen left-0 top-0 z-50">
            <div class="p-6 flex-grow overflow-y-auto">
                <div class="flex items-center gap-3 mb-10">
                    <img src="uploads/LOGO.png" alt="Logo" class="h-10 w-auto rounded-lg border-2 border-white shadow-lg" onerror="this.style.display='none'">
                    <h1 class="text-xl font-bold">Adidadidadas</h1>
                </div>

                <nav class="space-y-2">
                    <a href="dashboard.php" class="sidebar-btn flex items-center gap-3 px-4 py-3 rounded-lg <?php echo (!isset($_GET['content'])) ? 'bg-purple-700 shadow-inner font-bold' : 'hover:bg-purple-700/50'; ?>">
                        <i class="fas fa-home text-lg"></i>
                        <span>Dashboard</span>
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
                
                <?php if (!isset($_GET['content'])) : ?>
                <div class="mb-10 max-w-sm">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex items-center justify-between transition hover:shadow-md">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Total Products</p>
                            <h3 class="text-3xl font-bold text-slate-800 tracking-tight">
                                <?php echo number_format($totalProducts); ?>
                            </h3>
                        </div>
                        <div class="h-14 w-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600">
                            <i class="fas fa-box text-xl"></i>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div id="contentArea" class="w-full">
                    <?php
                    if(isset($_GET['content'])){
                        $content = $_GET['content'];
                        $pages = [
                            'manage_categories' => './manage_categories.php',
                            'add_product' => './add_product.php',
                            'add_category' => './add_category.php',
                            'manage_product' => './manage_product.php',
                            'user_log' => './user_log.php',
                            'edit_log' => './edit_log.php',
                            'edit_product' => './edit_product.php',
                            'edit_category' => './edit_category.php',
                            'settings' => './settings.php',
                            'history' => './order_history.php'
                        ];

                        if(array_key_exists($content, $pages) && file_exists($pages[$content])) {
                            include $pages[$content];
                        } else {
                            echo "<div class='p-12 bg-white rounded-3xl border border-dashed border-gray-200 text-center text-gray-400 font-bold'>
                                    <i class='fas fa-search mb-4 text-4xl block'></i> PAGE NOT FOUND
                                  </div>";
                        }
                    } else {
                        include './order_history.php'; 
                    }
                    ?>
                </div>
                    
                <footer class="mt-20 py-10 text-center border-t border-gray-200">
                    <p class="text-gray-400 text-[10px] uppercase tracking-widest">&copy; 2025 Adidadidadas | Admin System</p>
                </footer>
            </div>
        </div>
    </div>
</body>
</html>
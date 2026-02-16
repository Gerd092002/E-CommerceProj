<?php
/**
 * Reusable Sidebar Component para sa tanan pages
 */
?>
<div class="col-span-1 md:col-span-2">
    <div class="bg-white h-full rounded-2xl flex flex-col shadow-sm">
       
        <div class="h-26 flex items-center justify-center flex-col">
            <img class="w-16 mt-6 object-contain" src="uploads/LOGO.png" alt="Adidadidadas Shoes">
            <h1 class="font-bold mt-2 text-xl">ADIDADIDADAS</h1>
        </div>

        <div class="flex flex-col gap-4 mt-4 items-center p-4">
            
            <a href="dashboard.php" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-500' : ''; ?>">
                <svg class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
                <h1 class="text-2xl ms-2 <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Shop</h1>
            </a>

            <a href="cart.php" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'bg-blue-500' : ''; ?>">
                <svg class="<?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <h1 class="text-2xl ms-2 <?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Cart</h1>
            </a>

            <?php if (isset($_SESSION['userType']) && strtolower($_SESSION['userType']) === 'admin'): ?>
                
                <a href="add_product.php" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo basename($_SERVER['PHP_SELF']) === 'add_product.php' ? 'bg-blue-500' : ''; ?>">
                    <svg class="<?php echo basename($_SERVER['PHP_SELF']) === 'add_product.php' ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 16h6"/><path d="M19 13v6"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                        <path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/>
                    </svg>
                    <h1 class="text-2xl ms-2 <?php echo basename($_SERVER['PHP_SELF']) === 'add_product.php' ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Add Products</h1>
                </a>

                <a href="?content=manage_product" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo isset($_GET['content']) && $_GET['content'] === 'manage_product' ? 'bg-blue-500' : ''; ?>">
                    <i class="fas fa-tasks text-xl <?php echo isset($_GET['content']) && $_GET['content'] === 'manage_product' ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition ms-1"></i>
                    <h1 class="text-2xl ms-3 <?php echo isset($_GET['content']) && $_GET['content'] === 'manage_product' ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Manage Product</h1>
                </a>

                <a href="manage_users.php" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_users.php' || basename($_SERVER['PHP_SELF']) === 'edit_user.php') ? 'bg-blue-500' : ''; ?>">
                    <svg class="<?php echo (basename($_SERVER['PHP_SELF']) === 'manage_users.php' || basename($_SERVER['PHP_SELF']) === 'edit_user.php') ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.657-.671-3.157-1.76-4.233m2.76 4.233l.812 2.582m-4.572-8.725a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-9 6.75c1.657 0 3.157.671 4.233 1.76.985.99 1.644 2.338 1.821 3.721H3.75a4.125 4.125 0 0 1 4.275-5.481Zm0 0s3.063-.669 4.275-1.481" />
                    </svg>
                    <h1 class="text-2xl ms-2 <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_users.php' || basename($_SERVER['PHP_SELF']) === 'edit_user.php') ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Manage User</h1>
                </a>

                <a href="?content=settings" class="group hover:bg-blue-500 p-3 rounded-xl flex items-center w-full transition <?php echo isset($_GET['content']) && $_GET['content'] === 'settings' ? 'bg-blue-500' : ''; ?>">
                    <i class="fas fa-cog text-xl <?php echo isset($_GET['content']) && $_GET['content'] === 'settings' ? 'text-white' : 'text-blue-500'; ?> group-hover:text-white transition ms-1"></i>
                    <h1 class="text-2xl ms-3 <?php echo isset($_GET['content']) && $_GET['content'] === 'settings' ? 'text-white' : 'text-gray-500'; ?> group-hover:text-white transition">Settings</h1>
                </a>

            <?php endif; ?>
        </div>

        <div class="mt-auto p-4 w-full border-t border-gray-100">
            <a href="logout.php" class="group hover:bg-red-500 p-3 rounded-xl flex items-center w-full transition">
                <svg class="text-blue-500 group-hover:text-white transition" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m16 17 5-5-5-5"/>
                    <path d="M21 12H9"/>
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                </svg>
                <h1 class="text-2xl ms-4 text-gray-500 group-hover:text-white transition">Log Out</h1>
            </a>
        </div>
    </div>
</div>
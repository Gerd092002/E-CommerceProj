<?php
    include 'connection.php';
    session_start();
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
    <header class="gradient-bg shadow-lg ">
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
                
                <!-- ge change ang design sa Search bar ge addan  icon-->
                <div class="relative mx-auto  w-full  md:w-96">
                    <input 
                        class="w-full p-4 pl-12 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        type="text" 
                        placeholder="Search products, brands, and categories..."
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- ge change ang mga buttons nag add  og icons ug cart badge -->
                 <!-- desktop view -->
                <div class=" hidden lg:block ">
                   <div class="flex">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                       
                        
                        <a href="dashboard.php?content=cart" class=" bg-white text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-shopping-cart"></i>
                            Cart
                            <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                        </a>
                    <?php endif ; ?>
                     <?php if(isset($_SESSION['user_id'])) :?>
                        <a href="dashboard.php" class="bg-white ml-12 text-purple-600 px-6 py-3 rounded-full font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user"></i>
                            back to dashboard
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
                       
                        
                        <a href="dashboard.php?content=cart" class=" bg-white text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-shopping-cart"></i>
                            Cart
                            <span class="bg-red-500 text-white text-xs rounded-xl h-5 w-5 flex items-center justify-center">0</span>
                        </a>
                    <?php endif ; ?>
                     <?php if(isset($_SESSION['user_id'])) :?>
                        <a href="dashboard.php" class="bg-white  text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-user"></i>
                            back to dashboard
                        </a>
                    <?php else : ?>
                         <a href="login.html" class="bg-white  text-purple-600 px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg hover:bg-gray-100 transition">
                        <i class="fas fa-user"></i>
                        Login/Create
                    </a>
                    <?php endif ;?>
            </div>

        </div>
    </header>

    <!-- Nag add Navigation, nag add new categories pwede rasad home ra ibutang -->
   <! <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex flex-wrap justify-center gap-2 md:gap-6">
                <a href="#" class="category-btn bg-gray-100 text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-purple-100 hover:text-purple-700">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="#" class="category-btn bg-gray-100 text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-purple-100 hover:text-purple-700">
                    <i class="fas fa-shoe-prints mr-2"></i>Sneakers
                </a>
                <a href="#" class="category-btn bg-gray-100 text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-purple-100 hover:text-purple-700">
                    <i class="fas fa-tshirt mr-2"></i>Apparel
                </a>
                <a href="#" class="category-btn bg-gray-100 text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-purple-100 hover:text-purple-700">
                    <i class="fas fa-fire mr-2"></i>New Releases
                </a>
                <a href="#" class="category-btn bg-gray-100 text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-purple-100 hover:text-purple-700">
                    <i class="fas fa-percent mr-2"></i>Sales
                </a>
            </div>
        </div>
    </nav>

    <!-- Nag add Hero banner section  para sa promotions -->
    <section class="container mx-auto px-4 py-8 ">
        <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-2xl shadow-2xl p-8 text-white">
            <div class="max-w-xl">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Limited Edition Drops</h2>
                <p class="text-xl mb-6">Exclusive sneakers and apparel available only this week. Don't miss out!</p>
                <button class="bg-white text-purple-600 px-8 py-3 rounded-full font-bold text-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Shop Now <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- ge change ang Main content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Nag add ug Section header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
                <p class="text-gray-600">Discover our most popular items</p>
            </div>
            
        </div>

        <!-- ge usab ang design sa Product grid - gihimo nga white background ug naay shadow  -->
        <div class="w-full p-4 mx-auto bg-white rounded-2xl shadow-lg">
            <?php
            $show_add_to_cart = true;
            include 'components/product_grid.php';
            ?>
        </div>
        
        <!-- nag add ug Newsletter subscription section (pwede rag wala suggestion ra nis ai) -->
        <div class="mt-16 bg-gray-100 rounded-2xl p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Stay Updated</h3>
            <p class="text-gray-600 mb-6">Subscribe to get notified about new releases and exclusive offers</p>
            <div class="max-w-md mx-auto flex gap-2">
                <input type="email" placeholder="Your email address" class="flex-grow p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500">
                <button class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">Subscribe</button>
            </div>
        </div>
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
                    const cartBadge = document.querySelector('.bg-red-500');
                    if (cartBadge) {
                        const currentCount = parseInt(cartBadge.textContent) || 0;
                        cartBadge.textContent = currentCount + 1;
                    }
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
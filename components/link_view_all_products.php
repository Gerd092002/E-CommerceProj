 <!-- Nag add ug View All Button para sa non-admin users -->
               
                <?php if (isset($_SESSION['userType']) && (strtolower($_SESSION['userType']) !== 'admin' && strtolower($_SESSION['userType']) !== 'staff')) : ?>
                   
                    
                    
                    <div class="mt-8 text-center">
                        
                        <a href="index.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-600 transition-all duration-300">
                            <i class="fas fa-store"></i>
                            View All Products
                        </a>
                    </div>
                   
                <?php endif; ?>
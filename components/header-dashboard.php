 <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <?php if(isset($_SESSION['userType']) && strtolower($_SESSION['userType']) === 'admin' || strtolower($_SESSION['userType']) === 'staff') : ?>
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Welcome section  -->
                    <div class="flex-1">
                        <div class="text-gray-600">Welcome back!</div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <?php
                                if (isset($_SESSION['username'])) {
                                    echo htmlspecialchars($_SESSION['username']);
                                }
                            ?>
                        </h1>
                    </div>

                    <!-- ge change ang Search Bar -->
                    <!-- Input with search icon ug gradient button -->
                    <div class="flex-1 max-w-xl">
                        <form method="GET" class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search products..." 
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                            >
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-purple-600 to-pink-500 text-white px-4 py-1.5 rounded-lg hover:from-purple-700 hover:to-pink-600 transition-all duration-300 text-sm font-medium">
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- ge change ang  User Actions area  -->
                    
                    </div>

                <!-- Clear Search -->
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <div class="mt-4 flex justify-end">
                            <a href="dashboard.php" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-800 font-medium">
                                <i class="fas fa-times"></i>
                                Clear Search
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 font-medium">Total Products</div>
                            <div class="text-3xl font-bold text-gray-800 mt-2">120,000</div>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-full">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 font-medium">Products Sold</div>
                            <div class="text-3xl font-bold text-gray-800 mt-2">15,000</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-full">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 font-medium">Total Sales</div>
                            <div class="text-3xl font-bold text-gray-800 mt-2">₱500,000</div>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-full">
                            <i class="fas fa-coins text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            
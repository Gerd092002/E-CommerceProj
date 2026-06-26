<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// I-include ang sidebar/header kung gikinahanglan
// include 'components/sidebar.php'; 
?>

<div class="w-full max-w-5xl mx-auto py-8 px-4">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 rounded-t-2xl flex items-center gap-3">
    <i class="fas fa-plus-circle text-white text-xl"></i> 
    <h2 class="text-white font-bold uppercase tracking-wider text-sm">Add New Category</h2>
</div>
   

    <div class="bg-white rounded-b-2xl shadow-xl overflow-hidden">
        <form action="category_process.php" method="POST" class="p-8 space-y-6 text-left">
            
            <div>
                  <label class="block text-gray-700 font-semibold mb-1">
                    Category Name
                </label>
                <input type="text" 
                       name="name" 
                       placeholder="e.g. Electronics, Home Decor" 
                       required 
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all bg-gray-50/30">
            </div>

            <div>
                 <label class="block text-gray-700 font-semibold mb-1">
                    Description
                </label>
                <textarea name="description" 
                          rows="5" 
                          placeholder="Briefly describe what items belong in this category..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all bg-gray-50/30 resize-none"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" 
                        name="add_category" 
                        class="w-full bg-[#A855F7] hover:bg-purple-700 text-white py-4 rounded-xl font-black transition-all active:scale-95 uppercase tracking-widest text-[12px] shadow-lg shadow-purple-200">
                    SAVE CATEGORY 
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php?content=manage_categories" class="text-gray-500 hover:text-purple-600 font-medium transition-colors text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left text-[10px]"></i> Back to Manage Categories
                </a>
            </div>
        </form>
    </div>
</div>
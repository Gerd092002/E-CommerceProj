<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';

// Sigurohon nga naay ID nga gipasa sa URL
if (isset($_GET['id'])) {
    $category_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Pagkuha sa data sa kategorya
    $query = "SELECT * FROM tbl_categories WHERE category_id = '$category_id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $cat_name = $row['categoryName'];
        $cat_desc = $row['categoryDesc'];
    } else {
        echo "<script>alert('Category not found!'); window.location.href='dashboard.php?content=manage_categories';</script>";
        exit();
    }
}
?><div class="w-full max-w-5xl mx-auto py-8 px-4">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 rounded-t-2xl flex items-center gap-3 shadow-lg">
        <i class="fas fa-edit text-white text-xl"></i> 
        <div class="flex flex-col">
            <h2 class="text-white font-bold uppercase tracking-wider text-sm">Edit Category</h2>
            <p class="text-purple-100 text-[10px] uppercase">Update category details</p>
        </div>
    </div>

    <div class="bg-white rounded-b-2xl shadow-xl overflow-hidden">
        <form action="category_process.php" method="POST" class="p-8 space-y-6 text-left">
            
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">

            <div>
                   <label class="block text-gray-700 font-semibold mb-1">
                    Category Name
                </label>
                <input type="text" 
                       name="name" 
                       value="<?php echo htmlspecialchars($cat_name); ?>" 
                       required 
                       class="w-full px-4 py-4 border border-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all bg-gray-50/50">
            </div>

            <div>
                  <label class="block text-gray-700 font-semibold mb-1">
                    Description
                </label>
                <textarea name="description" 
                          rows="5" 
                          class="w-full px-4 py-4 border border-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all bg-gray-50/50 resize-none"><?php echo htmlspecialchars($cat_desc); ?></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" 
                        name="update_category" 
                        class="flex-1 bg-[#A855F7] hover:bg-purple-700 text-white py-4 rounded-xl font-black transition-all active:scale-95 uppercase tracking-widest text-[12px] shadow-lg shadow-purple-200">
                    SAVE CHANGES
                </button>
                 </div>
               <div class="text-center mt-4">
                <a href="dashboard.php?content=manage_product" class="text-gray-600 hover:text-blue-600 font-medium text-sm">
                    ← Cancel and Go Back
                </a>
            </div>
            </div>
        </form>
    </div>
</div>
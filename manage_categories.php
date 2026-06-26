<?php
// 1. KONEKSYON SA DATABASE
include 'connection.php';

// 2. LOGIC PARA SA PAG-DELETE
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM tbl_categories WHERE category_id = $id")) {
        echo "<script>alert('Category Deleted!'); window.location.href='dashboard.php?content=manage_categories';</script>";
    } else {
        echo "<script>alert('Deletion is not permitted. Ensure that this is no longer used in any product.'); window.location.href='dashboard.php?content=manage_categories';</script>";
    }
}
?>

<div class="w-full">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
        
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-5 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-tags text-white text-xl"></i> 
                <div>
                    <h2 class="text-white font-bold uppercase tracking-widest text-sm">Manage Categories</h2>
                    <p class="text-[10px] text-purple-100 uppercase tracking-wider">Organize your product classifications</p>
                </div>
            </div>

            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-white/60">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="categorySearch" onkeyup="searchCategories()" 
                       placeholder="Search category name or description..." 
                       class="w-full pl-10 pr-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 text-xs outline-none focus:bg-white focus:text-gray-800 transition-all shadow-inner">
            </div>

            <a href="?content=add_category" class="bg-white text-purple-600 px-4 py-2 rounded-xl hover:bg-purple-50 transition-all text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add New Category
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr class="text-[10px] font-semibold mb-1 text-gray-600 uppercase tracking-[0.2em]">
                        <th class="px-8 py-5 text-left w-24">ID</th>
                        <th class="px-8 py-5 text-left">Category Name</th>
                        <th class="px-8 py-5 text-left">Description</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $res = $conn->query("SELECT category_id, categoryName, categoryDesc FROM tbl_categories ORDER BY category_id DESC");
                    
                    if($res):
                        while ($row = $res->fetch_assoc()):
                    ?>
                    <tr class="category-row hover:bg-purple-50/20 transition-all">
                        <td class="px-8 py-5 text-sm font-medium text-gray-400">#<?php echo $row['category_id']; ?></td>
                        <td class="px-8 py-5">
                            <span class="text-[13px] font-semibold text-slate-700 uppercase tracking-tight">
                                <?php echo htmlspecialchars($row['categoryName']); ?>
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-sm text-gray-500 max-w-lg">
                                <?php echo htmlspecialchars($row['categoryDesc']); ?>
                            </p>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="?content=edit_category&id=<?php echo $row['category_id']; ?>" 
                                   class="h-9 w-9 flex items-center justify-center text-blue-500 bg-blue-50/50 rounded-xl hover:bg-blue-100 transition-all shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="?content=manage_categories&delete_id=<?php echo $row['category_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this?')" 
                                   class="h-9 w-9 flex items-center justify-center text-red-500 bg-red-50/50 rounded-xl hover:bg-red-100 transition-all shadow-sm">
                                    <i class="fas fa-trash text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addModal" class="hidden fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl transition-all scale-100">
        <div class="bg-purple-600 p-6 text-white flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-plus-circle"></i>
                <h3 class="font-bold uppercase tracking-widest text-xs">Add New Category</h3>
            </div>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="category_process.php" method="POST" class="p-6 space-y-5">
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Category Name</label>
                <input type="text" name="categoryName" required placeholder="e.g. Running Shoes"
                       class="w-full px-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all text-sm shadow-inner">
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Description</label>
                <textarea name="categoryDesc" rows="3" required placeholder="Describe this category..."
                          class="w-full px-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all text-sm shadow-inner"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" name="add_category" 
                        class="w-full bg-purple-600 text-white py-4 rounded-xl font-bold uppercase tracking-[0.2em] text-[10px] hover:bg-purple-700 shadow-lg shadow-purple-200 transition-all active:scale-[0.98]">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Search Function
function searchCategories() {
    let input = document.getElementById('categorySearch').value.toLowerCase();
    let rows = document.getElementsByClassName('category-row');
    for (let row of rows) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    }
}

// Close modal if click outside
window.onclick = function(event) {
    let modal = document.getElementById('addModal');
    if (event.target == modal) {
        modal.classList.add('hidden');
    }
}
</script>
<?php
// 1. DATABASE CONNECTION & INITIALIZATION
include 'connection.php';

// Kuhaon ang ID gikan sa URL
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>window.location.href='dashboard.php?content=manage_product';</script>";
    exit();
}

// FETCH DATA: I-fetch ang current product details
$stmt = $conn->prepare("SELECT * FROM tbl_products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// FETCH ALL CATEGORIES: Para sa dropdown list
$categories_result = $conn->query("SELECT * FROM tbl_categories ORDER BY categoryName ASC");

// 2. UPDATE LOGIC
if (isset($_POST['update_product'])) {
    $p_name   = $_POST['p_name'];
    $p_price  = $_POST['p_price'];
    $p_qty    = $_POST['p_qty'];
    $p_cat_id = $_POST['category_id']; // Nakuha gikan sa dropdown
    
    $image_to_save = $product['image_path']; 
    
    if (!empty($_FILES['p_image']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . '_' . basename($_FILES["p_image"]["name"]);
        $file_path = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["p_image"]["tmp_name"], $file_path)) {
            $image_to_save = $file_path; 
        }
    }

    // UPDATE PRODUCT TABLE: Gi-apil ang category_id sa pag-update
    $stmt1 = $conn->prepare("UPDATE tbl_products SET product_name=?, price=?, quantity=?, image_path=?, category_id=? WHERE product_id=?");
    $stmt1->bind_param("sdisii", $p_name, $p_price, $p_qty, $image_to_save, $p_cat_id, $id);
    
    if ($stmt1->execute()) {
        echo "<script>alert('Product Updated Successfully!'); window.location.href='dashboard.php?content=manage_product';</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "');</script>";
    }
}
?>

<div class="w-full max-w-5xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-purple-600 p-4 flex items-center gap-3">
            <i class="fas fa-edit text-white"></i>
            <h2 class="text-white font-bold uppercase tracking-wider text-sm">Edit Product Details</h2>
        </div>

        <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6 text-left">
            
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Category</label>
                <select name="category_id" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                    <option value="" disabled>-- Select Category --</option>
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>" 
                            <?php echo ($cat['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['categoryName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Name</label>
                <input type="text" name="p_name" required 
                       value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Price (₱)</label>
                    <input type="number" step="0.01" name="p_price" required 
                           value="<?php echo $product['price'] ?? 0; ?>" 
                           class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
                    <input type="number" name="p_qty" required 
                           value="<?php echo $product['quantity'] ?? 0; ?>" 
                           class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Product Image</label>
                <div class="flex items-center gap-6 p-4 border border-gray-100 rounded-2xl bg-gray-50/50 min-h-[120px]">
                    <div class="w-24 h-24 bg-white rounded-xl border border-gray-100 flex items-center justify-center overflow-hidden shadow-sm shrink-0">
                        <img id="edit_img_preview" src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                             class="<?php echo empty($product['image_path']) ? 'hidden' : ''; ?> w-full h-full object-contain p-2">
                        <i id="edit_img_icon" class="fas fa-image text-gray-200 text-2xl <?php echo !empty($product['image_path']) ? 'hidden' : ''; ?>"></i>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3">
                            <label class="bg-purple-50 text-purple-600 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer hover:bg-purple-100 transition-colors">
                                Choose New File
                                <input type="file" name="p_image" class="hidden" onchange="previewEditImage(this)">
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-400 italic">* Leave blank to keep current image</p>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_product" class="w-full bg-[#A855F7] hover:bg-purple-700 text-white py-4 rounded-xl font-black transition-all active:scale-95 uppercase tracking-widest text-[12px] shadow-lg shadow-purple-200">
                SAVE CHANGES TO INVENTORY
            </button>
            
            <div class="text-center mt-4">
                <a href="dashboard.php?content=manage_product" class="text-gray-600 hover:text-blue-600 font-medium text-sm">
                    ← Cancel and Go Back
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewEditImage(input) {
    const preview = document.getElementById('edit_img_preview');
    const icon = document.getElementById('edit_img_icon');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if(icon) icon.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = ""; 

// 1. I-fetch ang mga Categories para sa Dropdown
$categories_query = "SELECT * FROM tbl_categories ORDER BY categoryName ASC";
$categories_result = $conn->query($categories_query);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $category_id = $_POST['category_id']; // Nakuha gikan sa dropdown
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // File Upload Logic
    $fileName = time() . "_" . basename($_FILES["p_image"]["name"]); 
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowTypes = array('jpg','png','jpeg','gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        if (move_uploaded_file($_FILES["p_image"]["tmp_name"], $targetFilePath)) {
            
            // 2. I-insert na lang diretso sa tbl_products gamit ang napili nga category_id
            $sql_product = "INSERT INTO tbl_products (product_name, price, quantity, image_path, category_id) VALUES (?, ?, ?, ?, ?)";
            $stmt_prod = $conn->prepare($sql_product);
            $stmt_prod->bind_param("sdisi", $product_name, $price, $quantity, $targetFilePath, $category_id); 

            if ($stmt_prod->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Product DB Error: " . $conn->error;
            }
        } else {
            $message = "Error uploading file.";
        }
    } else {
        $message = "Invalid file type. Only JPG, PNG, JPEG, and GIF are allowed.";
    }
}
?>

<div class="w-full max-w-5xl mx-auto py-8">
    <?php if ($message != ""): ?>
        <script>alert("<?php echo $message; ?>");</script>
    <?php endif; ?>
  
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 rounded-t-2xl flex items-center gap-3">
    <i class="fas fa-plus-circle text-white text-xl"></i> 
    <h2 class="text-white font-bold uppercase tracking-wider text-sm">Add Product</h2>
</div>

        <form action="" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 text-left">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-1">Select Category</label>
                    <select name="category_id" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                        <option value="" disabled selected>-- Choose a Category --</option>
                        <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>">
                                <?php echo htmlspecialchars($cat['categoryName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Name</label>
                <input type="text" name="product_name" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Price (₱)</label>
                    <input type="number" step="0.01" name="price" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
                    <input type="number" name="quantity" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Product Image</label>
                <div class="flex items-center gap-6 p-4 border border-gray-100 rounded-2xl bg-gray-50/50 min-h-[120px]">
                    <div class="w-24 h-24 bg-white rounded-xl border border-gray-100 flex items-center justify-center overflow-hidden shadow-sm shrink-0">
                        <img id="img_preview" src="#" alt="preview" class="hidden w-full h-full object-contain p-2">
                        <i id="img_icon" class="fas fa-image text-gray-200 text-2xl"></i>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3">
                            <label class="bg-purple-50 text-purple-600 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer hover:bg-purple-100 transition-colors">
                                Choose File
                                <input type="file" name="p_image" id="p_image" required class="hidden" onchange="showPreview(this)">
                            </label>
                            <span id="file_name" class="text-[11px] text-gray-400">No file chosen</span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="add_product" class="w-full bg-[#A855F7] hover:bg-purple-700 text-white py-4 rounded-xl font-black transition-all active:scale-95 uppercase tracking-widest text-[12px] shadow-lg shadow-purple-200">
                SAVE PRODUCT TO INVENTORY
            </button>
            
             <div class="text-center mt-4">
                <a href="dashboard.php?content=manage_product" class="text-gray-500 hover:text-purple-600 font-medium transition-colors text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left text-[10px]"></i> Back to Manage Product
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function showPreview(input) {
    const preview = document.getElementById('img_preview');
    const icon = document.getElementById('img_icon');
    const fileName = document.getElementById('file_name');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        fileName.textContent = input.files[0].name;

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            icon.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
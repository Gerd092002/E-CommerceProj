<?php
// 1. KONEKSYON UG SECURITY CHECK
include 'connection.php';

// Sugod sa session kon wala pa kini nasugdan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Siguroha nga naka-login ang user
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

// SAKTONG COLUMN NAMES: 
// Gigamit nato ang 'user_id' kay mao kini ang naa sa imong tbl_user
$user_id = $_SESSION['user_id']; 
$profileColumn = mysqli_query($conn, "SHOW COLUMNS FROM tbl_user LIKE 'profile_picture'");
if ($profileColumn && mysqli_num_rows($profileColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE tbl_user ADD profile_picture VARCHAR(255) NULL AFTER userType");
}

$query = "
    SELECT u.*, c.customerID, c.fullname, c.phone, c.address
    FROM tbl_user u
    LEFT JOIN tbl_customers c ON c.user_id = u.user_id
    WHERE u.user_id = ?
    LIMIT 1
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // I-convert ang resulta ngadto sa array
    $user = $result->fetch_assoc();
} else {
    // Ipakita ang error kon naay problema sa SQL execution
    die("Error sa Database: " . $conn->error);
}

// I-set ang variable para sa Admin check
$isAdmin = (isset($_SESSION['userType']) && strtolower($_SESSION['userType']) === 'admin');
$status = $_GET['status'] ?? '';
$message = $_GET['message'] ?? '';
$profilePicture = !empty($user['profile_picture']) ? $user['profile_picture'] : '';
?>
<div class="p-4 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Settings</h1>
        <p class="text-gray-500">Manage your profile and system configurations.</p>
    </div>

    <?php if (!empty($status)): ?>
        <div class="mb-6 rounded-2xl border px-5 py-4 <?php echo $status === 'success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800'; ?>">
            <div class="flex items-center gap-3">
                <i class="fas <?php echo $status === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <span><?php echo htmlspecialchars($message ?: ($status === 'success' ? 'Profile updated successfully.' : 'Unable to update profile.')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-600">
                    <i class="fas fa-user-circle"></i> Edit Profile
                </h2>
                
                <form action="update_settings.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 rounded-2xl bg-gray-50 border border-gray-100 p-5">
                        <div class="h-24 w-24 rounded-full overflow-hidden bg-purple-100 border-4 border-white shadow flex items-center justify-center text-3xl font-bold text-purple-700">
                            <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile picture" class="h-full w-full object-cover">
                            <?php else: ?>
                                <?php echo strtoupper(substr($user['userName'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">Profile Picture</label>
                            <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-purple-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-purple-700">
                            <p class="text-xs text-gray-500">Accepted formats: JPG, PNG, GIF, or WEBP.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['userName']); ?>" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">User Type</label>
                            <input type="text" value="<?php echo strtoupper($user['userType']); ?>" disabled
                                   class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">Full Name</label>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? $user['userName']); ?>" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">Contact Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+63 9xxxxxxxxx"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition text-sm">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-600 uppercase">Address</label>
                        <textarea name="address" rows="3" placeholder="Enter your complete address"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition text-sm resize-none"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-lg font-bold mb-4 text-gray-700">Change Password</h3>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">New Password</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition pr-12 text-sm">
                               <button type="button" onclick="togglePass('new_password', 'eyeIconProfile')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 focus:outline-none">
                                         <i id="eyeIconProfile" class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($isAdmin): ?>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-600">
                    <i class="fas fa-user-plus"></i> Add New System User
                </h2>
                
                <form action="add_user_process.php" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase">Username</label>
                            <input type="text" name="userName" required placeholder="Enter username"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase">Email</label>
                            <input type="email" name="email" required placeholder="user@email.com"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all text-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase">Password</label>
                            <div class="relative">
                                <input type="password" id="add_user_pass" name="userPassword" required placeholder="••••••••"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all text-sm pr-12">
                               <button type="button" onclick="togglePass('add_user_pass', 'eyeIconAdd')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 focus:outline-none">
                                        <i id="eyeIconAdd" class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase">User Type</label>
                            <div class="relative">
                                <select name="userType" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all appearance-none text-sm cursor-pointer">
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="btn_save_user" class="w-full bg-purple-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                            Register Account
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-purple-700 to-indigo-800 rounded-3xl p-8 text-white shadow-xl text-center">
                <div class="flex justify-center mb-4">
                    <div class="h-24 w-24 rounded-full bg-white/20 overflow-hidden flex items-center justify-center text-3xl font-bold border-2 border-white/30 shadow-inner">
                        <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile picture" class="h-full w-full object-cover">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['userName'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <h3 class="text-xl font-bold"><?php echo htmlspecialchars($user['userName']); ?></h3>
                <p class="text-purple-200 text-sm opacity-80 uppercase tracking-widest"><?php echo $user['userType']; ?></p>
                <?php if (!empty($user['phone'])): ?>
                    <p class="text-purple-100 text-sm mt-4"><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($user['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($user['address'])): ?>
                    <p class="text-purple-100 text-sm mt-2"><i class="fas fa-location-dot mr-2"></i><?php echo htmlspecialchars($user['address']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Generic function para sa pag-toggle sa password visibility
 */
function togglePass(inputId, iconId) {
    const pInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (pInput.type === 'password') {
        // Kon i-click samtang naka-password (gitago), ipakita ang text
        pInput.type = 'text';
        // Usbon ang icon ngadto sa abli nga mata
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        // Kon i-click samtang gipakita na, ibalik sa password (itago)
        pInput.type = 'password';
        // Ibalik sa sirado nga mata
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>

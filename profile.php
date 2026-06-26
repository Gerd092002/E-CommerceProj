<?php
/**
 * User Profile View Page - Read-only Profile Display
 */

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = intval($_SESSION['user_id']);

// Fetch user info with profile picture
$userQuery = mysqli_query($conn, "SELECT user_id, userName, email, userType, profile_picture FROM tbl_user WHERE user_id = $uid");
$userData = ($userQuery && mysqli_num_rows($userQuery) > 0) ? mysqli_fetch_assoc($userQuery) : [];

// Fetch customer info
$custQuery = mysqli_query($conn, "SELECT * FROM tbl_customers WHERE user_id = $uid");
$customer_data = ($custQuery && mysqli_num_rows($custQuery) > 0) ? mysqli_fetch_assoc($custQuery) : [];

// Get profile picture
$profilePicture = !empty($userData['profile_picture']) && file_exists($userData['profile_picture']) ? $userData['profile_picture'] : null;
?>

<div class="max-w-3xl mx-auto">
    <!-- Profile Header with Picture -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-3xl shadow-lg p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Profile Picture -->
            <div class="flex-shrink-0">
                <div class="h-40 w-40 rounded-full bg-white/20 border-4 border-white flex items-center justify-center overflow-hidden shadow-lg">
                    <?php if ($profilePicture): ?>
                        <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile picture" class="h-full w-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-user text-7xl text-white"></i>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold mb-2">
                    <?php echo htmlspecialchars($userData['userName'] ?? 'User'); ?>
                </h1>
                
                <div class="space-y-3 text-purple-100">
                    <p class="flex items-center justify-center md:justify-start gap-3">
                        <i class="fas fa-envelope text-lg"></i>
                        <span><?php echo htmlspecialchars($userData['email'] ?? 'No email'); ?></span>
                    </p>
                    <p class="flex items-center justify-center md:justify-start gap-3">
                        <i class="fas fa-badge text-lg"></i>
                        <span><?php echo ucfirst(htmlspecialchars($userData['userType'] ?? 'Customer')); ?></span>
                    </p>
                    <?php if (!empty($customer_data['phone'])): ?>
                    <p class="flex items-center justify-center md:justify-start gap-3">
                        <i class="fas fa-phone text-lg"></i>
                        <span><?php echo htmlspecialchars($customer_data['phone']); ?></span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information Display -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-info-circle mr-3 text-purple-600"></i>Profile Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Full Name -->
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Full Name</p>
                <p class="text-lg text-gray-800 font-medium">
                    <?php echo htmlspecialchars($customer_data['fullname'] ?? 'Not set'); ?>
                </p>
            </div>

            <!-- Phone -->
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Phone Number</p>
                <p class="text-lg text-gray-800 font-medium">
                    <?php echo htmlspecialchars($customer_data['phone'] ?? 'Not set'); ?>
                </p>
            </div>


            <!-- User Type -->
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Account Type</p>
                <p class="text-lg text-gray-800 font-medium">
                    <span class="inline-block px-3 py-1 rounded-full text-white text-sm <?php echo strtolower($userData['userType']) === 'admin' ? 'bg-red-500' : (strtolower($userData['userType']) === 'staff' ? 'bg-blue-500' : 'bg-green-500'); ?>">
                        <?php echo ucfirst(htmlspecialchars($userData['userType'] ?? 'Customer')); ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Address -->
        <?php if (!empty($customer_data['address'])): ?>
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Address</p>
            <p class="text-lg text-gray-800">
                <?php echo htmlspecialchars($customer_data['address']); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="mb-8">
        <a 
            href="dashboard.php?content=settings" 
            class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-pink-600 transition shadow-lg text-center block"
        >
            <i class="fas fa-edit mr-2"></i>Edit Profile
        </a>
    </div>
</div>

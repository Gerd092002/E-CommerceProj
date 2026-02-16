<?php

include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';
$customer_data = [];

// Fetch existing customer info from session or database
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $query = mysqli_query($conn, "SELECT * FROM tbl_user WHERE user_id = $uid");
    if ($query && mysqli_num_rows($query) > 0) {
        $customer_data = mysqli_fetch_assoc($query);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $bday = mysqli_real_escape_string($conn, trim($_POST['bday'] ?? ''));


    // Validation
    if (empty($full_name) || empty($email)) {
        $error_message = "Full name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
       
        // Check if user has existing info
        $uid = intval($_SESSION['user_id']);
        $check = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = $uid");
        
        if ($check && mysqli_num_rows($check) > 0) {
            // Update existing
            $update = "UPDATE tbl_customers SET 
                fullname = '$full_name',
                email = '$email',
                phone = '$phone',
                address = '$address',
                bday = '$bday',
                user_id = '$uid'
                WHERE user_id = $uid";
            if (mysqli_query($conn, $update)) {
                $success_message = "Your information has been updated successfully!";
            } else {
                $error_message = "Error updating information: " . mysqli_error($conn);
            }
        } else {
            // Insert new
            $insert = "INSERT INTO tbl_customers (fullname,email,phone, address, bday,user_id) 
                VALUES (?,?,?,?,?,?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param('sssssi',$full_name,$email,$phone,$address,$bday,$uid);
            $stmt->execute();
            if ($stmt->excute()) {
                $success_message = "Your information has been saved successfully!";
                
            } else {
                $error_message = "Error saving information: " . mysqli_error($conn);
            }
        }

        // Refresh customer data
        if (empty($error_message)) {
            $query = mysqli_query($conn, "SELECT * FROM tbl_customers WHERE user_id = $uid");
            if ($query && mysqli_num_rows($query) > 0) {
                $customer_data = mysqli_fetch_assoc($query);
                $customerID = $customer_data['customerID'];
                $_SESSION['customerID'] = $customerID;
            }
        }
    }
}

function render_customer_info_form($customer_data, $success_message, $error_message) {
    ob_start();
    ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Customer Information</h2>
            <p class="text-sm text-gray-500 mt-1">Update your personal and contact information</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800"><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-red-800"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        value="<?php echo htmlspecialchars($customer_data['full_name'] ?? ''); ?>"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        placeholder="John Doe"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_SESSION['email'] ?? $customer_data['email'] ?? ''); ?>"
                        required 
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                    >
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?php echo htmlspecialchars($customer_data['phone'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        placeholder="+63 9xxxxxxxxx"
                    >
                </div>

                <!-- Address -->
                <div>
                    <label for="Address" class="block text-sm font-semibold text-gray-700 mb-2">
                        Address
                    </label>
                    <input 
                        type="text" 
                        id="Address" 
                        name="Address" 
                        value="<?php echo htmlspecialchars($customer_data['address'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        placeholder="Metro Manila"
                    >
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="bday" class="block text-sm font-semibold text-gray-700 mb-2">
                        Day of Birth
                    </label>
                    <input 
                        type="date" 
                        id="bday" 
                        name="bday" 
                        value="<?php echo htmlspecialchars($customer_data['bday'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        placeholder="1234"
                    >
                </div>
            </div>


            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-600 transition-all duration-300"
                >
                    <i class="fas fa-save mr-2"></i> Save Information
                </button>
                <a 
                    href="dashboard.php" 
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-300"
                >
                    Cancel
                </a>
            </div>
        </form>

        <!-- Info Card -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold">Keep your information up to date</p>
                    <p class="text-blue-700 mt-1">This information will be used for order delivery and communication purposes.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// If this file is included into another page (like dashboard.php), only output the inner content.
if (basename($_SERVER['PHP_SELF']) !== basename(__FILE__)) {
    echo render_customer_info_form($customer_data, $success_message, $error_message);
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Customer Information - Adidadidadas</title>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">
    <div class="p-6">
       
        <?php echo render_customer_info_form($customer_data, $success_message, $error_message); ?>
    </div>
</body>
</html>

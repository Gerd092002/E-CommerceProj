<?php
session_start();
include 'connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_SESSION['userType']) && strtolower($_SESSION['userType']) !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';
$user = null;

if (isset($_GET['id'])) {
    // Convert ID to integer for safety
    $user_id = intval($_GET['id']);
    
    // fetch user information
    $sql = "SELECT user_id, userName, email, userType FROM tbl_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows > 0) {
        
        $user = $result->fetch_assoc();
    } else {
        // If user not found - redirect back to manage users page
        header("Location: manage_users.php");
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $usertype = htmlspecialchars($_POST['usertype'] ?? '');
    
    
    if (empty($username) || empty($email) || empty($usertype)) {
        $error = "All fields are required!";
    } else {
     
        $sql = "UPDATE tbl_user SET userName = ?, email = ?, userType = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $usertype, $user_id);
        

        if ($stmt->execute()) {
        
            $success = "User updated successfully!";
            $user['userName'] = $username;
            $user['email'] = $email;
            $user['userType'] = $usertype;
        } else {
            $error = "Error updating user: " . mysqli_error($conn);
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit User</title>
</head>
<body>
    <div class="min-h-screen bg-gray-200 px-3 py-5 grid grid-cols-1 md:grid-cols-12 gap-4">

        <?php include 'components/sidebar.php'; ?>

        <div class="bg-gray-200 rounded-2xl col-span-1 md:col-span-10">

            <div class="bg-white h-20 rounded-xl w-full p-2 flex justify-between items-center">
                <div class="ps-6 w-full">
                    <span>Edit User</span>
                    <?php
                        if (isset($_SESSION['username'])) {
                            echo '<h1 class="text-2xl font-bold text-blue-500">' . htmlspecialchars($_SESSION['username']) . '</h1>';
                        }
                    ?>
                </div>

                <div class="flex justify-end items-center w-full p-4">
                    <div class="p-4 flex gap-2 items-center max-w-xs sm:max-w-sm md:max-w-md overflow-hidden">
                        <h1 class="text-3xl text-gray-400">|</h1>
                        <?php
                            if (isset($_SESSION['userType'])) {
                                echo '<div class="flex items-center gap-1 truncate">
                                        <h1 class="text-2xl text-gray-400 pt-1 truncate">' . htmlspecialchars($_SESSION['userType']) . '</h1>
                                      </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            
            <div class="w-full p-6">
                <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
                    <h3 class="text-2xl font-bold mb-6">Edit User</h3>

                    <?php if ($error): ?>
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($user): ?>
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">User ID</label>
                                <input type="text" disabled class="w-full px-4 py-2 border rounded bg-gray-100" value="<?php echo $user['user_id']; ?>">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Username</label>
                                <input type="text" name="username" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($user['userName']); ?>">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                                <input type="email" name="email" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none" value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">User Type</label>
                                <select name="usertype" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="Admin" <?php echo $user['userType'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="User" <?php echo $user['userType'] === 'User' ? 'selected' : ''; ?>>User</option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition font-semibold">
                                    Save Changes
                                </button>
                                <a href="manage_users.php" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition font-semibold">
                                    Back
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-red-500">User not found</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

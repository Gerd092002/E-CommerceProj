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

// Handle user deletion if delete_id is provided
if (isset($_GET['delete_id'])) {
    // Get user ID to delete and convert to integer for safety
    $delete_id = intval($_GET['delete_id']);
    // Ensure we're not deleting the first admin user
    $delete_id = max(1, $delete_id);
    
    $sql = "DELETE FROM tbl_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php?message=User deleted successfully");
        exit();
    } else {
        $error = "Error deleting user: " . mysqli_error($conn);
    }
    $stmt->close();
}


$error = isset($error) ? $error : '';

$sql = "SELECT user_id, userName, email, userType FROM tbl_user ORDER BY user_id DESC";
$result = mysqli_query($conn, $sql);
$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manage Users</title>
</head>
<body>
    <div class="min-h-screen bg-gray-200 px-3 py-5 grid grid-cols-1 md:grid-cols-12 gap-4">

        <?php include 'components/sidebar.php'; ?>

        <div class="bg-gray-200 rounded-2xl col-span-1 md:col-span-10">

            <div class="bg-white h-20 rounded-xl w-full p-2 flex justify-between items-center">
                <div class="ps-6 w-full">
                    <span>Manage Users</span>
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

            <!-- Users Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">User ID</th>
                                <th class="px-6 py-4 text-left font-semibold">Username</th>
                                <th class="px-6 py-4 text-left font-semibold">Email</th>
                                <th class="px-6 py-4 text-left font-semibold">User Type</th>
                                <th class="px-6 py-4 text-center font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-4"><?php echo $user['user_id']; ?></td>
                                        <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($user['userName']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded text-white text-sm <?php echo strtolower($user['userType']) === 'admin' ? 'bg-blue-500' : 'bg-gray-500'; ?>">
                                                <?php echo htmlspecialchars($user['userType']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="text-blue-500 hover:text-blue-700 font-semibold">
                                                    Edit
                                                </a>
                                                <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                                    <a href="manage_users.php?delete_id=<?php echo $user['user_id']; ?>" class="text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Are you sure you want to delete this user?');">
                                                        Delete
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

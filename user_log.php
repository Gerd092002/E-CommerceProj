<?php
// Sugod sa output buffering para malikayan ang header errors
ob_start(); 

// 1. DATABASE CONNECTION
include 'connection.php';

// I-set ang timezone sa Pilipinas para match ang date display
date_default_timezone_set('Asia/Manila');

// 2. SQL QUERY - Siguroha nga gi-select ang 'remarks'
$sql = "SELECT * FROM tbl_user_logs ORDER BY login_time DESC";
$result = $conn->query($sql);
?>

<div class="flex flex-col min-h-screen"> 
    
    <div class="p-6 flex-grow">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">User Activity Logs</h1>
                <div class="mt-1 inline-block bg-purple-100 text-purple-700 px-4 py-1 rounded-full text-sm font-semibold border border-purple-200">
                    <i class="fas fa-circle text-[10px] mr-1 animate-pulse"></i> Live Monitoring
                </div>
            </div>

            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="logSearchInput" onkeyup="searchLogs()" 
                       placeholder="Search username, role, or status..." 
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none transition shadow-sm bg-white">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="logsTable">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Log ID</th>
                            <th class="px-6 py-4 text-left">Username</th>
                            <th class="px-6 py-4 text-left">Role</th>
                            <th class="px-6 py-4 text-left">Login Time</th>
                            <th class="px-6 py-4 text-left">Logout Time</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Remarks</th>
                            <th class="px-6 py-4 text-center">Actions</th> 
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="log-row hover:bg-purple-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-mono text-xs">#<?php echo $row['log_id']; ?></td>
                                <td class="px-6 py-4 font-semibold text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                                
                                <td class="px-6 py-4">
                                    <?php 
                                        $role = strtoupper($row['userType']);
                                        $class = "bg-gray-100 text-gray-600 border-gray-200"; // Default
                                        if ($role === 'ADMIN') $class = "bg-red-100 text-red-600 border-red-200";
                                        if ($role === 'STAFF') $class = "bg-blue-100 text-blue-600 border-blue-200";
                                        if ($role === 'CUSTOMER') $class = "bg-green-100 text-green-600 border-green-200";
                                    ?>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border <?php echo $class; ?>">
                                        <?php echo htmlspecialchars($role); ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    <i class="far fa-clock mr-1.5 text-gray-300 text-xs"></i>
                                    <?php echo date('M d, Y | h:i A', strtotime($row['login_time'])); ?>
                                </td>
                                
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    <?php 
                                        if (!empty($row['logout_time'])) {
                                            echo '<i class="fas fa-sign-out-alt mr-1.5 text-gray-300 text-xs"></i>';
                                            echo date('M d, Y | h:i A', strtotime($row['logout_time']));
                                        } else {
                                            echo '<span class="text-gray-300 italic">---</span>';
                                        }
                                    ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if (empty($row['logout_time'])): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                            <span class="h-1.5 w-1.5 bg-green-500 rounded-full mr-2 animate-pulse"></span> Online
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-500 border border-gray-100">
                                            <span class="h-1.5 w-1.5 bg-gray-300 rounded-full mr-2"></span> Offline
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-xs text-gray-500 italic max-w-[150px] truncate">
                                    <?php echo htmlspecialchars($row['remarks'] ?? '---'); ?>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="dashboard.php?content=edit_log&id=<?php echo $row['log_id']; ?>" class="text-blue-500 hover:text-blue-700 mr-3 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_log.php?id=<?php echo $row['log_id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center bg-gray-50/30">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-history text-4xl text-gray-200 mb-4"></i>
                                        <p class="text-gray-400 italic">No user activity logs found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>

<script>
function searchLogs() {
    const filter = document.getElementById("logSearchInput").value.toLowerCase();
    const rows = document.getElementsByClassName("log-row");

    for (let i = 0; i < rows.length; i++) {
        const rowText = rows[i].innerText.toLowerCase();
        rows[i].style.display = rowText.includes(filter) ? "" : "none";
    }
}
</script>
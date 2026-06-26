<?php
include 'connection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php?content=user_log");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM tbl_user_logs WHERE log_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (isset($_POST['update_log'])) {
    $new_status = $_POST['status'];
    $new_role   = $_POST['userType'];
    $remarks    = $_POST['remarks']; 
    
    $update_sql = "UPDATE tbl_user_logs SET status = ?, userType = ?, remarks = ? WHERE log_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    if ($update_stmt) {
        $update_stmt->bind_param("sssi", $new_status, $new_role, $remarks, $id);
        if ($update_stmt->execute()) {
            echo "<script>alert('Log Updated Successfully!'); window.location.href='dashboard.php?content=user_log';</script>";
            exit();
        }
    }
}
?>

<div class="min-h-screen flex flex-col bg-gray-50/20">
    <div class="flex-grow p-6">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-purple-600 p-4">
                <h2 class="text-white font-bold flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Activity Log #<?php echo $id; ?>
                </h2>
            </div>
            
            <form method="POST" class="p-6 space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($row['username']); ?>" disabled 
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-gray-500 cursor-not-allowed">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Role/User Type</label>
                        <select name="userType" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 outline-none">
                            <option value="ADMIN" <?php echo ($row['userType'] == 'ADMIN') ? 'selected' : ''; ?>>ADMIN</option>
                            <option value="STAFF" <?php echo ($row['userType'] == 'STAFF') ? 'selected' : ''; ?>>STAFF</option>
                            <option value="CUSTOMER" <?php echo ($row['userType'] == 'CUSTOMER') ? 'selected' : ''; ?>>CUSTOMER</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Status</label>
                        <select name="status" id="statusSelect" onchange="updateStatusColor()"
                                class="w-full border-2 rounded-lg px-4 py-2 outline-none transition-all
                                <?php echo ($row['status'] == 'Logged In') ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500'; ?>">
                            <option value="Logged In" <?php echo ($row['status'] == 'Logged In') ? 'selected' : ''; ?>>🟢 Logged In (Online)</option>
                            <option value="Logged Out" <?php echo ($row['status'] == 'Logged Out') ? 'selected' : ''; ?>>⚪ Logged Out (Offline)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Admin Remarks / Notes</label>
                    <textarea name="remarks" rows="3" placeholder="Reason for log modification? (e.g., Force logout due to system crash)" 
                              class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 outline-none text-sm"><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-50">
                    <a href="dashboard.php?content=user_log" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Cancel</a>
                    <button type="submit" name="update_log" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-md text-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


</div>

<script>
function updateStatusColor() {
    const select = document.getElementById('statusSelect');
    if (select.value === 'Logged In') {
        select.className = "w-full border-2 border-green-200 bg-green-50 text-green-700 rounded-lg px-4 py-2 outline-none transition-all";
    } else {
        select.className = "w-full border-2 border-gray-200 bg-gray-50 text-gray-500 rounded-lg px-4 py-2 outline-none transition-all";
    }
}
</script>
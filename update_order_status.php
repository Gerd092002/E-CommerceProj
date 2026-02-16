<?php
include 'connection.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$order_id = intval($data['order_id']);
$status = strtolower(trim($data['status']));
$allowed = ['active','purchased','removed','saved'];
if (!in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

// detect whether tbl_orders uses customerID or user_id
$uid = intval($_SESSION['user_id']);
$user_col = 'user_id';
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) {
    $user_col = 'customerID';
}

$user_val = $uid;
if ($user_col === 'customerID') {
    if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
        $user_val = intval($_SESSION['customerID']);
    } else {
        $custRes = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . $uid . " LIMIT 1");
        if ($custRes && mysqli_num_rows($custRes) > 0) {
            $crow = mysqli_fetch_assoc($custRes);
            $user_val = intval($crow['customerID']);
            $_SESSION['customerID'] = $user_val;
        }
    }
}

// Build query with safe column name
$sql = "UPDATE tbl_orders SET status = ? WHERE order_id = ? AND " . $user_col . " = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed']);
    exit;
}
$stmt->bind_param('sii', $status, $order_id, $user_val);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

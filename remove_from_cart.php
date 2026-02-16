<?php
session_start();

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Parse JSON data from AJAX request
$data = json_decode(file_get_contents('php://input'), true);
// Get product ID to remove from cart
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}

// Check if product exists sa cart
// If user logged in, mark the tbl_orders row as removed
if (isset($_SESSION['user_id'])) {
    include 'connection.php';
    $user_id = intval($_SESSION['user_id']);
    // detect user column and resolve value
    $user_col = 'user_id';
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");
    if ($colCheck && mysqli_num_rows($colCheck) > 0) {
        $user_col = 'customerID';
    }
    $user_val = $user_id;
    if ($user_col === 'customerID') {
        if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
            $user_val = intval($_SESSION['customerID']);
        } else {
            $custRes = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . $user_id . " LIMIT 1");
            if ($custRes && mysqli_num_rows($custRes) > 0) {
                $crow = mysqli_fetch_assoc($custRes);
                $user_val = intval($crow['customerID']);
                $_SESSION['customerID'] = $user_val;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'No customer record found.']);
                exit();
            }
        }
    }
    $sql = "UPDATE tbl_orders SET status = 'removed' WHERE $user_col = ? AND product_id = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_val, $product_id);
    $stmt->execute();

    // return remaining active count
    $countSql = "SELECT COUNT(*) as cnt FROM tbl_orders WHERE $user_col = ? AND status = 'active'";
    $cstmt = $conn->prepare($countSql);
    $cstmt->bind_param('i', $user_val);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    $crow = $cres->fetch_assoc();
    $remaining = intval($crow['cnt'] ?? 0);

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Item removed from cart', 'cart_count' => $remaining]);
    exit();

} else {
    // Remove item from cart session
    if (!isset($_SESSION['cart'][$product_id])) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not in cart']);
        exit();
    }
    unset($_SESSION['cart'][$product_id]);

    // Send success response with updated cart count
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit();
}
?>

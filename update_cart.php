<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
// Get product ID and new quantity
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
$qty = isset($data['qty']) ? intval($data['qty']) : 0;

// Validate input parameters
if ($product_id <= 0 || $qty <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID or quantity']);
    exit();
}

// Check if product exists in cart
// Query database to check available stock
$sql = "SELECT quantity FROM tbl_products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Product not found in database
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit();
}

// Get product stock info
$product = $result->fetch_assoc();

// Check if requested quantity exceeds available stock
if ($qty > $product['quantity']) {
    http_response_code(400);
    echo json_encode(['error' => 'Insufficient stock. Available: ' . $product['quantity']]);
    exit();
}

// If user logged in, update tbl_orders
if (isset($_SESSION['user_id'])) {
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
                echo json_encode(['error' => 'No customer record found. Please complete your customer information.']);
                exit();
            }
        }
    }
    $checkSql = "SELECT order_id FROM tbl_orders WHERE $user_col = ? AND product_id = ? AND status = 'active' LIMIT 1";
    $cstmt = $conn->prepare($checkSql);
    $cstmt->bind_param('ii', $user_val, $product_id);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    if ($cres && $cres->num_rows > 0) {
        $r = $cres->fetch_assoc();
        $orderId = intval($r['order_id']);
        $updateSql = "UPDATE tbl_orders SET quantity = ? WHERE order_id = ?";
        $ustmt = $conn->prepare($updateSql);
        $ustmt->bind_param('ii', $qty, $orderId);
        $ustmt->execute();
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Cart updated', 'new_qty' => $qty]);
        exit();
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Item not in cart']);
        exit();
    }

} else {
    // Update quantity in session cart
    if (!isset($_SESSION['cart'][$product_id])) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not in cart']);
        exit();
    }
    $_SESSION['cart'][$product_id]['qty'] = $qty;
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'new_qty' => $qty
    ]);
    exit();
}

$stmt->close();
$conn->close();
?>

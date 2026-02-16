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
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
$qty = isset($data['qty']) ? intval($data['qty']) : 1;

if ($product_id <= 0 || $qty <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID or quantity']);
    exit();
}

// Fetch product info
$sql = "SELECT product_name, price, quantity FROM tbl_products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit();
}

$product = $result->fetch_assoc();

if ($qty > $product['quantity']) {
    http_response_code(400);
    echo json_encode(['error' => 'Insufficient stock. Available: ' . $product['quantity']]);
    exit();
}

// Ensure tbl_orders exists
$createOrders = "CREATE TABLE IF NOT EXISTS tbl_orders (
    order_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active','purchased','removed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createOrders);

$response = ['success' => false];

// If user is logged in, persist to tbl_orders, otherwise fallback to session cart
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    // detect whether tbl_orders uses customerID or user_id
    $user_col = 'user_id';
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM tbl_orders LIKE 'customerID'");
    if ($colCheck && mysqli_num_rows($colCheck) > 0) {
        $user_col = 'customerID';
    }

    // Resolve value for the user column (user_id or customerID)
    $user_val = $user_id;
    if ($user_col === 'customerID') {
        if (isset($_SESSION['customerID']) && intval($_SESSION['customerID']) > 0) {
            $user_val = intval($_SESSION['customerID']);
        } else {
            // try to lookup in tbl_customers
            $custRes = mysqli_query($conn, "SELECT customerID FROM tbl_customers WHERE user_id = " . $user_id . " LIMIT 1");
            if ($custRes && mysqli_num_rows($custRes) > 0) {
                $crow = mysqli_fetch_assoc($custRes);
                $user_val = intval($crow['customerID']);
                $_SESSION['customerID'] = $user_val;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'No customer record found. Please complete your customer information before adding to cart.']);
                exit();
            }
        }
    }

    // Check if there's an active order item for this user and product
    $checkSql = "SELECT order_id, quantity FROM tbl_orders WHERE $user_col = ? AND product_id = ? AND status = 'active' LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('ii', $user_val, $product_id);
    $checkStmt->execute();
    $checkRes = $checkStmt->get_result();

    if ($checkRes && $checkRes->num_rows > 0) {
        $row = $checkRes->fetch_assoc();
        $new_qty = intval($row['quantity']) + $qty;
        if ($new_qty > $product['quantity']) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient stock. Max available: ' . $product['quantity']]);
            exit();
        }
        $updateSql = "UPDATE tbl_orders SET quantity = ?, price = ? WHERE order_id = ?";
        $upd = $conn->prepare($updateSql);
        $p_price = floatval($product['price']);
        $upd->bind_param('idi', $new_qty, $p_price, $row['order_id']);
        $upd->execute();
        $response['message'] = 'Cart updated';
        $response['item_qty'] = $new_qty;
        $checkStmt->close();
    } else {
        // Insert new order row
        $insertSql = "INSERT INTO tbl_orders ($user_col, product_id, quantity, price, status) VALUES (?, ?, ?, ?, 'active')";
        $ins = $conn->prepare($insertSql);
        $p_price = floatval($product['price']);
        $ins->bind_param('iiid', $user_val, $product_id, $qty, $p_price);
        $ins->execute();
        if ($ins->affected_rows > 0) {
            $response['message'] = 'Product added to cart';
            $response['item_qty'] = $qty;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add to cart']);
            exit();
        }
        $ins->close();
    }

    // Return cart count from DB
    $countSql = "SELECT COUNT(*) as cnt FROM tbl_orders WHERE $user_col = ? AND status = 'active'";
    $cstmt = $conn->prepare($countSql);
    $cstmt->bind_param('i', $user_val);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    $cRow = $cres->fetch_assoc();
    $response['cart_count'] = intval($cRow['cnt']);
    $cstmt->close();

    // Ensure success flag is accurate and return response
    $response['success'] = true;
    echo json_encode($response);
    $stmt->close();
    $conn->close();
    exit();

} else {
    // Fallback to session-based cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $new_qty = $_SESSION['cart'][$product_id]['qty'] + $qty;
        if ($new_qty > $product['quantity']) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient stock. Max available: ' . $product['quantity']]);
            exit();
        }
        $_SESSION['cart'][$product_id]['qty'] = $new_qty;
    } else {
        $_SESSION['cart'][$product_id] = [
            'qty' => $qty,
            'price' => floatval($product['price']),
            'name' => $product['product_name']
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'cart_count' => count($_SESSION['cart']),
        'item_qty' => $_SESSION['cart'][$product_id]['qty']
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

?>

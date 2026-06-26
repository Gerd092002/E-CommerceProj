<?php
session_start();
include 'connection.php';

$cartItems = [];
$total = 0;
$continueShoppingUrl = isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php';

// 1. Load cart items based on login status (Database for logged-in, Session for guests)
if (isset($_SESSION['user_id'])) {
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
    
    // Fetch active cart items from database
    $sql = "
    SELECT o.order_id, o.product_id, o.quantity, p.price, p.product_name, p.image_path
    FROM tbl_orders o
    LEFT JOIN tbl_products p ON o.product_id = p.product_id
    WHERE o.$user_col = ? AND o.status = 'active'
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_val);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $pid = intval($r['product_id']);
            $cartItems[$pid] = [
                'qty' => intval($r['quantity']),
                'price' => floatval($r['price']),
                'name' => $r['product_name'] ?? 'Product',
                'image_path' => $r['image_path'] ?? '',
                'order_id' => $r['order_id']
            ];
        }
    }
    $stmt->close();
} else {
    // Fallback for guest users
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $cartItems = $_SESSION['cart'];
}

if (count($cartItems) === 0) {
    header("Location: cart.php");
    exit();
}

foreach ($cartItems as $product_id => $item) {
    $subtotal = $item['price'] * $item['qty'];
    $total += $subtotal;
}

$payment_method = $_POST['payment_method'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($payment_method)) {
        $error = "All fields are required!";
    } else {
        $_SESSION['order_summary'] = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'payment_method' => $payment_method,
            'items' => $cartItems,
            'total' => $total,
            'order_date' => date('Y-m-d H:i:s')
        ];
        
        // Clear session cart (for guests)
        unset($_SESSION['cart']);
        
        // 2. If logged in, update database to mark items as 'purchased' so they leave the cart
        if (isset($_SESSION['user_id']) && isset($user_col) && isset($user_val)) {
            $updateSql = "UPDATE tbl_orders SET status = 'purchased' WHERE $user_col = ? AND status = 'active'";
            $ustmt = $conn->prepare($updateSql);
            $ustmt->bind_param('i', $user_val);
            $ustmt->execute();
            $ustmt->close();
        }
        
        header("Location: order_confirmation.php");
        exit();
    }
}
?>
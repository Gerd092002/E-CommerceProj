<?php
session_start();
include 'connection.php';

function redirect_settings($status, $message)
{
    header('Location: dashboard.php?content=settings&status=' . urlencode($status) . '&message=' . urlencode($message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);
$new_username = trim($_POST['username'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$fullname = trim($_POST['fullname'] ?? $new_username);
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($new_username === '') {
    redirect_settings('error', 'Username is required.');
}

$profileColumn = mysqli_query($conn, "SHOW COLUMNS FROM tbl_user LIKE 'profile_picture'");
if ($profileColumn && mysqli_num_rows($profileColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE tbl_user ADD profile_picture VARCHAR(255) NULL AFTER userType");
}

$profile_picture_path = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        redirect_settings('error', 'Profile picture upload failed. Please try again.');
    }

    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);

    if (!isset($allowed_types[$file_type])) {
        redirect_settings('error', 'Please upload a valid JPG, PNG, GIF, or WEBP image.');
    }

    if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
        redirect_settings('error', 'Profile picture must be 2MB or smaller.');
    }

    $extension = $allowed_types[$file_type];
    $profile_picture_path = 'uploads/profile_' . $user_id . '_' . time() . '.' . $extension;

    if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path)) {
        redirect_settings('error', 'Unable to save the uploaded profile picture.');
    }
}

if ($profile_picture_path) {
    $sql = "UPDATE tbl_user SET userName = ?, profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $new_username, $profile_picture_path, $user_id);
} else {
    $sql = "UPDATE tbl_user SET userName = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $new_username, $user_id);
}

if (!$stmt->execute()) {
    redirect_settings('error', 'Unable to update your profile.');
}
$stmt->close();

if ($new_password !== '') {
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $password_sql = "UPDATE tbl_user SET userPassword = ? WHERE user_id = ?";
    $password_stmt = $conn->prepare($password_sql);
    $password_stmt->bind_param('si', $hashed_password, $user_id);
    $password_stmt->execute();
    $password_stmt->close();
}

$user_sql = "SELECT email FROM tbl_user WHERE user_id = ? LIMIT 1";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_row = $user_result->fetch_assoc();
$email = $user_row['email'] ?? '';
$user_stmt->close();

$check_sql = "SELECT customerID FROM tbl_customers WHERE user_id = ? LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('i', $user_id);
$check_stmt->execute();
$customer_result = $check_stmt->get_result();
$customer_row = $customer_result->fetch_assoc();
$check_stmt->close();

if ($customer_row) {
    $customer_sql = "UPDATE tbl_customers SET fullname = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
    $customer_stmt = $conn->prepare($customer_sql);
    $customer_stmt->bind_param('ssssi', $fullname, $email, $phone, $address, $user_id);
} else {
    $customer_sql = "INSERT INTO tbl_customers (fullname, email, phone, address, user_id) VALUES (?, ?, ?, ?, ?)";
    $customer_stmt = $conn->prepare($customer_sql);
    $customer_stmt->bind_param('ssssi', $fullname, $email, $phone, $address, $user_id);
}

if (!$customer_stmt->execute()) {
    redirect_settings('error', 'Profile saved, but contact details could not be updated.');
}
$customer_stmt->close();

$_SESSION['username'] = $new_username;
redirect_settings('success', 'Profile updated successfully.');
?>

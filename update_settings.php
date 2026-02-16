<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_username = $_POST['username'];
    $new_password = $_POST['new_password'];

    // 1. I-update ang Username
    $sql = "UPDATE tbl_user SET userName = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_username, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username; // I-update ang session para mausab ang name sa dashboard
        
        // 2. I-update ang Password kon naay gisulod ang user
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $sql_pass = "UPDATE tbl_user SET userPassword = ? WHERE id = ?";
            $stmt_pass = $conn->prepare($sql_pass);
            $stmt_pass->bind_param("si", $hashed_password, $user_id);
            $stmt_pass->execute();
        }

        // Redirect balik sa settings nga naay success message
        header("Location: dashboard.php?content=settings&status=success");
    } else {
        header("Location: dashboard.php?content=settings&status=error");
    }
    exit();
}
?>
<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo 'Palihug pun-a ang username ug password.';
        exit;
    }
    
    $sql = "SELECT user_id AS id, userName, userPassword, userType FROM tbl_user WHERE userName = ? LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        
        if ($row && password_verify($password, $row['userPassword'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['userName'];
            $_SESSION['userType'] = $row['userType'];

            // Record login
            $log_sql = "INSERT INTO tbl_user_logs (user_id, username, userType, status) VALUES (?, ?, ?, 'Logged In')";
            if ($log_stmt = $conn->prepare($log_sql)) {
                $log_stmt->bind_param("iss", $row['id'], $row['userName'], $row['userType']);
                if ($log_stmt->execute()) {
                    $_SESSION['current_log_id'] = $conn->insert_id; 
                }
                $log_stmt->close();
            }
            
            header('Location: dashboard.php');
            exit();
        } else {
            echo 'Username or Password does not match.';
        }
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $conn->error);
        echo "Internal error, please try later.";
    }
} // KINI ANG PARES SA LINE 5
?>
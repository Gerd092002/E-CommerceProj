<?php
session_start();
include 'connection.php';

function redirect_with_login_error($message, $username = '')
{
    $_SESSION['login_error'] = $message;
    $_SESSION['login_username'] = $username;
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        redirect_with_login_error('Please enter your username/email and password.', $username);
    }
    
    $sql = "SELECT user_id AS id, userName, email, userPassword, userType FROM tbl_user WHERE userName = ? OR email = ? LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        
        if ($row && password_verify($password, $row['userPassword'])) {
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['userName'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['userType'] = $row['userType'];
            unset($_SESSION['login_error'], $_SESSION['login_username']);

            // Record login
            $log_sql = "INSERT INTO tbl_user_logs (user_id, username, userType, status) VALUES (?, ?, ?, 'Logged In')";
            if ($log_stmt = $conn->prepare($log_sql)) {
                $log_stmt->bind_param("iss", $row['id'], $row['userName'], $row['userType']);
                if ($log_stmt->execute()) {
                    $_SESSION['current_log_id'] = $conn->insert_id; 
                }
                $log_stmt->close();
            }
            
            $userType = strtolower($row['userType']);
            if ($userType === 'admin' || $userType === 'staff') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        }

        $stmt->close();
        redirect_with_login_error('The username/email or password you entered is incorrect. Please check your details and try again.', $username);
    } else {
        error_log("Prepare failed: " . $conn->error);
        redirect_with_login_error('We could not process your login right now. Please try again later.', $username);
    }
}

header('Location: login.php');
exit();
?>

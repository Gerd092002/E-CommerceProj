<?php
include 'connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check access
if (!isset($_SESSION['userType']) || strtolower($_SESSION['userType']) !== 'admin') {
    echo "<script>alert('Access Denied!'); window.location.href='dashboard.php';</script>";
    exit();
}

if (isset($_POST['btn_save_user'])) {
    $uName = mysqli_real_escape_string($conn, $_POST['userName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $uPass = $_POST['userPassword']; 
    $uType = mysqli_real_escape_string($conn, $_POST['userType']);

    // 1. IMPORTANTE: I-hash ang password para mo-match sa imong password_verify()
    $hashed_pass = password_hash($uPass, PASSWORD_DEFAULT);

    // 2. I-INSERT ang hashed password sa database
    $sql = "INSERT INTO tbl_user (userName, email, userPassword, userType) 
            VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $uName, $email, $hashed_pass, $uType);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Account Created Successfully!');
                    window.location.href='dashboard.php?page=settings';
                  </script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
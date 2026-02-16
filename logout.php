<?php
// logout.php - Ang purpose niini mao ang pag-record sa logout time ug paglimpyo sa session.
session_start();
include 'connection.php';

// Kon naay log_id nga gi-save pag-login, i-update ang database record
if (isset($_SESSION['current_log_id'])) {
    $current_log_id = $_SESSION['current_log_id'];
    
    // SQL: Butangan og oras ang logout_time ug usbon ang status
    $sql_update = "UPDATE tbl_user_logs 
                   SET logout_time = CURRENT_TIMESTAMP, 
                       status = 'Logged Out' 
                   WHERE log_id = '$current_log_id'";
    $conn->query($sql_update);
}

// Human sa update, i-clear ang tanang session data
session_unset();
session_destroy();

// I-balik ang user sa login page
header("Location: index.php");
exit();
?>
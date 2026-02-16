<?php

include 'connection.php';

session_start();

// Siguroha nga naay ID nga gi-pasa

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // 1. (Optional) Kuhaon ang image path para ma-delete sad ang file sa folder

    $sql_get = "SELECT image_path FROM tbl_products WHERE product_id = ?";

    $stmt_get = $conn->prepare($sql_get);

    $stmt_get->bind_param("i", $id);

    $stmt_get->execute();

    $result = $stmt_get->get_result();

    if ($row = $result->fetch_assoc()) {

        if (file_exists($row['image_path'])) {

            unlink($row['image_path']); // Tangtangon ang file sa 'uploads' folder
        }
    }
    // 2. I-delete ang record sa database

    $sql_del = "DELETE FROM tbl_products WHERE product_id = ?";

    $stmt_del = $conn->prepare($sql_del);

    $stmt_del->bind_param("i", $id);

    if ($stmt_del->execute()) {

        echo "<script>alert('Product deleted successfully!'); window.location='dashboard.php?content=manage_product';</script>";

    } else {
        echo "<script>alert('Error deleting product.'); window.location='dashboard.php?content=manage_product';</script>";
    }
}
?>
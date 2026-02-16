<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $category_name = $_POST['category_name'];
    $descCategoryName = $_POST['descCategoryName'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

   
    $sql = "UPDATE tbl_categories AS c
            INNER JOIN tbl_products AS p ON p.category_id = c.category_id
            SET c.categoryName = ?, c.categoryDesc = ?,
                p.product_name = ?, p.price = ?, p.quantity = ?
            WHERE p.product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddi", $category_name, $descCategoryName, $product_name, $price, $quantity, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update product.');</script>";
    }
}
?>

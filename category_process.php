<?php
include 'connection.php';

// Gi-check kung gi-click ba ang submit button
// Note: Sa imong HTML button, wala kay nilabay nga name="add_category"
// Mas maayo i-check nato kung naay sulod ang $_POST['name']
if (isset($_POST['name'])) {
    
    // 1. Kuhaon ang data gikan sa form
    // Gi-match ang names base sa imong HTML (name="name" ug name="description")
    $cat_name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat_desc = mysqli_real_escape_string($conn, $_POST['description']);

    // 2. SQL INSERT
    $sql = "INSERT INTO tbl_categories (categoryName, categoryDesc) VALUES ('$cat_name', '$cat_desc')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New Category Added!'); window.location.href='dashboard.php?content=manage_categories';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
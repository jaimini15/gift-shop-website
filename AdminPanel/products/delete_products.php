<?php
include("../db.php");

$id = $_GET['id'];

// Fetch image for deletion
$data = mysqli_fetch_assoc(mysqli_query($connection, 
    "SELECT Product_Image FROM Product_Details WHERE Product_Id=$id"
));

$image = $data['Product_Image'];

if(file_exists("uploads/".$image)){
    unlink("uploads/".$image); // delete image
}

// Delete product row
mysqli_query($connection, "DELETE FROM Product_Details WHERE Product_Id=$id");

header("Location: products.php?msg=deleted");
?>

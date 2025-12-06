<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "error";
    exit;
}

if (!isset($_POST['id'])) {
    echo "error";
    exit;
}

$uid = $_SESSION['User_Id'];
$id  = $_POST['id'];

// Validate item belongs to user
$check = mysqli_query($connection, "
    SELECT ccd.Customize_Id 
    FROM customize_cart_details ccd 
    JOIN cart c ON ccd.Cart_Id = c.Cart_Id 
    WHERE ccd.Customize_Id = '$id' AND c.User_Id = '$uid'
");

if (mysqli_num_rows($check) == 0) {
    echo "error";
    exit;
}

// Delete
$sql = "DELETE FROM customize_cart_details WHERE Customize_Id = '$id'";
echo mysqli_query($connection, $sql) ? "success" : "error";
?>

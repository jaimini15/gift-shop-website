<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['buy_now_flow'])) exit;

$uid = $_SESSION['User_Id'];

mysqli_query($connection,"
DELETE c, cd FROM cart c
LEFT JOIN customize_cart_details cd ON c.Cart_Id = cd.Cart_Id
WHERE c.User_Id='$uid'
");

unset($_SESSION['buy_now_flow']);
?>
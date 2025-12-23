<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "login";
    exit;
}

if (!isset($_POST['customize_id'])) {
    echo "invalid";
    exit;
}

$uid = $_SESSION['User_Id'];
$cid = (int)$_POST['customize_id'];

$sql = "
DELETE ccd FROM customize_cart_details ccd
JOIN cart c ON ccd.Cart_Id = c.Cart_Id
WHERE ccd.Customize_Id = '$cid'
AND c.User_Id = '$uid'
";

mysqli_query($connection, $sql);

echo "success";

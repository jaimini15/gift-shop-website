<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo 0;
    exit;
}

$uid = (int)$_SESSION['User_Id'];

$query = "
    SELECT SUM(Quantity) AS total
    FROM customize_cart_details ccd
    JOIN cart c ON c.Cart_Id = ccd.Cart_Id
    WHERE c.User_Id = $uid
";
$res = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($res);
echo (int)($row['total'] ?? 0);

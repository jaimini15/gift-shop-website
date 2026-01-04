<?php
session_start();
include("../AdminPanel/db.php");

header('Content-Type: text/plain; charset=utf-8');

if (!isset($_SESSION['cart_id'])) {
    echo "error: cart not found";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: invalid method";
    exit;
}

if (empty($_POST['id'])) {
    echo "error: missing id";
    exit;
}

$customizeId = (int)$_POST['id'];
$cartId = (int)$_SESSION['cart_id'];

// Verify item belongs to THIS cart
$stmt = mysqli_prepare($connection, "
    SELECT Customize_Id
    FROM customize_cart_details
    WHERE Customize_Id = ? AND Cart_Id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "ii", $customizeId, $cartId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) === 0) {
    echo "error: item not found";
    exit;
}
mysqli_stmt_close($stmt);

// Delete item
$del = mysqli_prepare(
    $connection,
    "DELETE FROM customize_cart_details WHERE Customize_Id = ? AND Cart_Id = ?"
);
mysqli_stmt_bind_param($del, "ii", $customizeId, $cartId);

if (mysqli_stmt_execute($del)) {
    echo "success";
} else {
    echo "error: delete failed";
}
mysqli_stmt_close($del);

<?php
session_start();
include("../AdminPanel/db.php");

header('Content-Type: text/plain; charset=utf-8');

if (!isset($_SESSION['User_Id'])) {
    echo "error: not logged in";
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

$id = (int)$_POST['id'];
$uid = (int)$_SESSION['User_Id'];

// Verify item belongs to user's cart
$sql = "
    SELECT ccd.Customize_Id
    FROM customize_cart_details ccd
    JOIN cart c ON ccd.Cart_Id = c.Cart_Id
    WHERE ccd.Customize_Id = ? AND c.User_Id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) === 0) {
    echo "error: item not found or not yours";
    exit;
}
mysqli_stmt_close($stmt);

// Delete
$del = mysqli_prepare($connection, "DELETE FROM customize_cart_details WHERE Customize_Id = ?");
mysqli_stmt_bind_param($del, "i", $id);
if (mysqli_stmt_execute($del)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($connection);
}
mysqli_stmt_close($del);

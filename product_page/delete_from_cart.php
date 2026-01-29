<?php
session_start();
include("../AdminPanel/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["status" => "error", "msg" => "not logged in"]);
    exit;
}

$userId = (int)$_SESSION['User_Id'];
$customizeId = (int)($_POST['id'] ?? 0);

if ($customizeId <= 0) {
    echo json_encode(["status" => "error", "msg" => "invalid id"]);
    exit;
}

/* Delete item securely */
$stmt = mysqli_prepare($connection, "
    DELETE ccd
    FROM customize_cart_details ccd
    JOIN cart c ON ccd.Cart_Id = c.Cart_Id
    WHERE ccd.Customize_Id = ? AND c.User_Id = ?
");
mysqli_stmt_bind_param($stmt, "ii", $customizeId, $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);  // ✅ Close statement

unset($_SESSION['stock_popup_shown']);

/* Get updated subtotal */
$stmt = mysqli_prepare($connection, "
    SELECT IFNULL(SUM(Price * Quantity), 0)
    FROM customize_cart_details
    WHERE Cart_Id = (SELECT Cart_Id FROM cart WHERE User_Id = ?)
");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $subtotal);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);  // ✅ Close statement

echo json_encode([
    "status"   => "success",
    "subtotal" => (float)$subtotal  // use float
]);

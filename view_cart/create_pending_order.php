<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'], $_SESSION['total'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit;
}

$userId = $_SESSION['User_Id'];
$total  = $_SESSION['total'];

/* Get Cart */
$cartRes = mysqli_query($connection, "SELECT Cart_Id FROM cart WHERE User_Id='$userId'");
$cart = mysqli_fetch_assoc($cartRes);

if (!$cart) {
    echo json_encode(["success" => false, "error" => "Cart empty"]);
    exit;
}

$cartId = $cart['Cart_Id'];

mysqli_begin_transaction($connection);

try {

    /* Insert Order (PENDING) */
    $stmt = mysqli_prepare($connection, "
        INSERT INTO `order` (User_Id, Total_Amount, Status)
        VALUES (?, ?, 'PENDING')
    ");
    mysqli_stmt_bind_param($stmt, "id", $userId, $total);
    mysqli_stmt_execute($stmt);

    $orderId = mysqli_insert_id($connection);
    $already = mysqli_query($connection,"
    SELECT 1 FROM order_item
    WHERE Order_Id = $orderId
    LIMIT 1
");

if (mysqli_num_rows($already) > 0) {
    mysqli_commit($connection);
    echo json_encode(["success"=>true,"order_id"=>$orderId]);
    exit;
}


    /* Insert Order Items */

    while ($row = mysqli_fetch_assoc($items)) {
        mysqli_stmt_bind_param(
            $itemStmt,
            "iiidss",
            $orderId,
            $row['Product_Id'],
            $row['Quantity'],
            $row['Price'],
            $row['Custom_Text'],
            $row['Custom_Image']
        );
        mysqli_stmt_execute($itemStmt);
    }

    $_SESSION['pending_order_id'] = $orderId;

    mysqli_commit($connection);

    echo json_encode(["success" => true, "order_id" => $orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success" => false, "error" => "Order creation failed"]);
}

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

    /* Insert Order Items */
    $items = mysqli_query($connection, "
        SELECT Product_Id, Quantity, Price, Custom_Text, Custom_Image
        FROM customize_cart_details
        WHERE Cart_Id='$cartId'
    ");

    $itemStmt = mysqli_prepare($connection, "
        INSERT INTO order_item
        (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

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

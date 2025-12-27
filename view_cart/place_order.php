<?php
session_start();
include("../AdminPanel/db.php");

/* --------------------
   BASIC VALIDATIONS
-------------------- */
if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

if (empty($_POST['payment_method'])) {
    echo json_encode(["success" => false, "message" => "Payment method required"]);
    exit;
}

$userId         = $_SESSION['User_Id'];
$totalAmount    = $_SESSION['total'];
$paymentMethod  = $_POST['payment_method'];

mysqli_begin_transaction($connection);

try {

    /* --------------------
       GET CART ID
    -------------------- */
    $cartRes = mysqli_query(
        $connection,
        "SELECT Cart_Id FROM cart WHERE User_Id = '$userId'"
    );

    $cart = mysqli_fetch_assoc($cartRes);
    if (!$cart) {
        throw new Exception("Cart not found");
    }

    $cartId = $cart['Cart_Id'];

    /* --------------------
       INSERT ORDER (PENDING)
    -------------------- */
    $orderSql = "INSERT INTO `order`
        (User_Id, Total_Amount, Status)
        VALUES (?, ?, 'PENDING')";

    $stmtOrder = mysqli_prepare($connection, $orderSql);
    mysqli_stmt_bind_param($stmtOrder, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmtOrder);

    $orderId = mysqli_insert_id($connection);

    /* --------------------
       INSERT ORDER ITEMS
    -------------------- */
    $itemsQuery = mysqli_query($connection, "
        SELECT Product_Id, Quantity, Price, Custom_Text, Custom_Image
        FROM customize_cart_details
        WHERE Cart_Id = '$cartId'
    ");

    $itemSql = "INSERT INTO order_item
        (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image)
        VALUES (?, ?, ?, ?, ?, ?)";

    $stmtItem = mysqli_prepare($connection, $itemSql);

    while ($row = mysqli_fetch_assoc($itemsQuery)) {
        mysqli_stmt_bind_param(
            $stmtItem,
            "iiidss",
            $orderId,
            $row['Product_Id'],
            $row['Quantity'],
            $row['Price'],
            $row['Custom_Text'],
            $row['Custom_Image']
        );
        mysqli_stmt_execute($stmtItem);
    }

    mysqli_commit($connection);

    /* --------------------
       STORE PENDING ORDER
    -------------------- */
    $_SESSION['pending_order_id'] = $orderId;

    echo json_encode([
        "success"  => true,
        "order_id" => $orderId
    ]);
    exit;

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
    exit;
}

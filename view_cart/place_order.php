<?php
session_start();
include("../AdminPanel/db.php");

/* --------------------
   BASIC VALIDATIONS
-------------------- */
if (!isset($_SESSION['User_Id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_POST['payment_method'])) {
    $_SESSION['payment_error'] = "Please select a payment option";
    header("Location: payment.php");
    exit;
}

$userId        = $_SESSION['User_Id'];
$totalAmount  = $_SESSION['total'];
$paymentMethod = $_POST['payment_method'];

/* --------------------
   START TRANSACTION
-------------------- */
mysqli_begin_transaction($connection);

try {

    /* --------------------
       GET CART ID
    -------------------- */
    $cartRes = mysqli_query($connection,
        "SELECT Cart_Id FROM cart WHERE User_Id = '$userId'"
    );

    $cart = mysqli_fetch_assoc($cartRes);
    if (!$cart) {
        throw new Exception("Cart not found");
    }

    $cartId = $cart['Cart_Id'];

    /* --------------------
       INSERT INTO order
    -------------------- */
    $orderSql = "INSERT INTO `order`
        (User_Id, Total_Amount, Status)
        VALUES (?, ?, 'PENDING')";

    $stmtOrder = mysqli_prepare($connection, $orderSql);
    mysqli_stmt_bind_param($stmtOrder, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmtOrder);

    $orderId = mysqli_insert_id($connection);

    /* --------------------
       FETCH CART ITEMS
    -------------------- */
    $itemsQuery = mysqli_query($connection, "
        SELECT Product_Id, Quantity, Price, Custom_Text, Custom_Image
        FROM customize_cart_details
        WHERE Cart_Id = '$cartId'
    ");

    /* --------------------
       INSERT INTO order_item
    -------------------- */
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

    /* --------------------
       CLEAR CART
    -------------------- */
    mysqli_query($connection, "DELETE FROM customize_cart_details WHERE Cart_Id='$cartId'");
    mysqli_query($connection, "DELETE FROM cart WHERE Cart_Id='$cartId'");

    /* --------------------
       COMMIT
    -------------------- */
    mysqli_commit($connection);

    header("Location: order_summary.php?order_id=$orderId");
    exit;

} catch (Exception $e) {

    mysqli_rollback($connection);
    die("Order Failed: " . $e->getMessage());
}

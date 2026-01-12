<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'], $_SESSION['pending_order_id'])) {
    echo json_encode(["success"=>false,"error"=>"Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['payment_method'])) {
    echo json_encode(["success"=>false,"error"=>"Payment method missing"]);
    exit;
}
$userId  = (int) $_SESSION['User_Id'];
$orderId = (int) $_SESSION['pending_order_id'];
$method  = mysqli_real_escape_string($connection, $data['payment_method']);
$isBuyNow = !empty($_SESSION['buy_now']);

mysqli_begin_transaction($connection);
try {

    /* 1️⃣ Confirm order */
   mysqli_query($connection,"
    UPDATE `order`
    SET Status='CONFIRM'
    WHERE Order_Id=$orderId
      AND User_Id=$userId
      AND Status='PENDING'
");

if (mysqli_affected_rows($connection) !== 1) {
    throw new Exception("Order already confirmed or invalid");
}

$checkPay = mysqli_query($connection,"
    SELECT 1 FROM payment_details WHERE Order_Id = $orderId
");
if (mysqli_num_rows($checkPay) > 0) {
    throw new Exception("Payment already recorded");
}

    /* 2️⃣ Insert payment */
    $txn = "TXN".time().rand(1000,9999);
    mysqli_query($connection,"
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        SELECT Order_Id, NOW(), '$method', Total_Amount, 'SUCCESS', '$txn'
        FROM `order`
        WHERE Order_Id=$orderId
    ");

    /* ================= BUY NOW FLOW ================= */
if ($isBuyNow) {

    $item = $_SESSION['buy_now_item'] ?? null;
    if (!$item) {
        throw new Exception("Buy now item missing");
    }

    $pid = (int)$item['product_id'];
    $qty = 1;
    $basePrice = (float)$item['price'];

$giftWrapPrice = !empty($item['gift_wrap']) ? 39 : 0;
$giftCardPrice = !empty($item['gift_card']) ? 50 : 0;

$priceSnapshot = $basePrice + $giftWrapPrice + $giftCardPrice;


   
    $stmt = mysqli_prepare($connection, "
    INSERT INTO order_item (
        Order_Id,
        Product_Id,
        Quantity,
        Price_Snapshot,
        Custom_Text,
        Gift_Wrapping,
        Personalized_Message,
        Custom_Image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$customText = $item['custom_text'] ?: null;
$giftMsg    = $item['gift_msg'] ?: null;
$customImg  = $item['custom_image']; // BINARY
$giftWrap   = (int)$item['gift_wrap'];
$null       = null;
mysqli_stmt_bind_param(
    $stmt,
    "iiidssib",
    $orderId,
    $pid,
    $qty,
    $priceSnapshot,
    $customText,
    $giftWrap,
    $giftMsg,
    $null 
);

mysqli_stmt_send_long_data($stmt, 7, $customImg); // VERY IMPORTANT

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


    /* Reduce stock */
    mysqli_query($connection,"
        UPDATE stock_details
        SET Stock_Available = Stock_Available - 1,
            Last_Update = NOW()
        WHERE Product_Id = $pid
          AND Stock_Available >= 1
    ");

    if (mysqli_affected_rows($connection) === 0) {
        throw new Exception("Product out of stock");
    }

}
/* ================= CART FLOW (UNCHANGED) ================= */
else {

    $cartItems = mysqli_query($connection,"
        SELECT c.*
        FROM customize_cart_details c
        JOIN cart ca ON ca.Cart_Id = c.Cart_Id
        WHERE ca.User_Id = $userId
    ");

    if (mysqli_num_rows($cartItems) == 0) {
        mysqli_commit($connection);
        echo json_encode(["success"=>true,"order_id"=>$orderId]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($cartItems)) {

        $pid = (int)$row['Product_Id'];
        $priceSnapshot = (float)$row['Price'];
        $qty = (int)$row['Quantity'];


        mysqli_query($connection,"
            INSERT INTO order_item (
                Order_Id,
                Product_Id,
                Quantity,
                Price_Snapshot,
                Custom_Text,
                Gift_Wrapping,
                Personalized_Message
            ) VALUES (
                $orderId,
                $pid,
                $qty,
                $priceSnapshot,
                ".($row['Custom_Text'] ? "'".mysqli_real_escape_string($connection,$row['Custom_Text'])."'" : "NULL").",
                {$row['Gift_Wrapping']},
                ".($row['Personalized_Message'] ? "'".mysqli_real_escape_string($connection,$row['Personalized_Message'])."'" : "NULL")."
            )
        ");

        mysqli_query($connection,"
            UPDATE stock_details
            SET Stock_Available = Stock_Available - $qty,
                Last_Update = NOW()
            WHERE Product_Id = $pid
              AND Stock_Available >= $qty
        ");

        if (mysqli_affected_rows($connection) === 0) {
            throw new Exception("Product ID $pid is out of stock");
        }
    }

    /* CLEAR CART — CART FLOW ONLY */
    mysqli_query($connection,"
        DELETE c FROM customize_cart_details c
        JOIN cart ca ON ca.Cart_Id = c.Cart_Id
        WHERE ca.User_Id = $userId
    ");

    mysqli_query($connection,"
        DELETE FROM cart WHERE User_Id = $userId
    ");
}

    unset($_SESSION['pending_order_id']);
    unset($_SESSION['buy_now'], $_SESSION['buy_now_item'], $_SESSION['buy_now_product_id']);


    mysqli_commit($connection);

    echo json_encode(["success"=>true,"order_id"=>$orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}

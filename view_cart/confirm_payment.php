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

mysqli_begin_transaction($connection);

try {

    /* 1️⃣ Confirm order */
    mysqli_query($connection,"
        UPDATE `order`
        SET Status='CONFIRM'
        WHERE Order_Id=$orderId AND User_Id=$userId
    ");

    /* 2️⃣ Insert payment */
    $txn = "TXN".time().rand(1000,9999);
    mysqli_query($connection,"
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        SELECT Order_Id, NOW(), '$method', Total_Amount, 'SUCCESS', '$txn'
        FROM `order`
        WHERE Order_Id=$orderId
    ");

    /* 3️⃣ FETCH CART ITEMS (CRITICAL) */
    $cartItems = mysqli_query($connection,"
        SELECT c.*
        FROM customize_cart_details c
        JOIN cart ca ON ca.Cart_Id = c.Cart_Id
        WHERE ca.User_Id = $userId
    ");

    if (mysqli_num_rows($cartItems) == 0) {
        throw new Exception("Cart empty at payment time");
    }

    /* 4️⃣ INSERT INTO order_item */
    while ($row = mysqli_fetch_assoc($cartItems)) {

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
                {$row['Product_Id']},
                {$row['Quantity']},
                {$row['Price']},
                ".($row['Custom_Text'] ? "'".mysqli_real_escape_string($connection,$row['Custom_Text'])."'" : "NULL").",
                {$row['Gift_Wrapping']},
                ".($row['Personalized_Message'] ? "'".mysqli_real_escape_string($connection,$row['Personalized_Message'])."'" : "NULL")."
            )
        ");
    }

    /* 5️⃣ CLEAR CART AFTER INSERT */
    mysqli_query($connection,"
        DELETE c FROM customize_cart_details c
        JOIN cart ca ON ca.Cart_Id = c.Cart_Id
        WHERE ca.User_Id = $userId
    ");

    mysqli_query($connection,"
        DELETE FROM cart WHERE User_Id = $userId
    ");

    unset($_SESSION['pending_order_id']);

    mysqli_commit($connection);

    echo json_encode(["success"=>true,"order_id"=>$orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}

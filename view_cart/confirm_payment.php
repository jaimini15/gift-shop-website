<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['pending_order_id'], $_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "No pending order"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$razorpay_payment_id = $data['razorpay_payment_id'] ?? "";

if (empty($razorpay_payment_id)) {
    echo json_encode(["success" => false, "error" => "Invalid payment"]);
    exit;
}

$order_id = (int)$_SESSION['pending_order_id'];
$user_id  = (int)$_SESSION['User_Id'];

$check = mysqli_query($connection, "
    SELECT 1 FROM payment_details WHERE Order_Id = '$order_id' LIMIT 1
");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success" => true, "order_id" => $order_id]);
    exit;
}

mysqli_begin_transaction($connection);

try {
    $orderRes = mysqli_query($connection, "
        SELECT Total_Amount FROM `order` WHERE Order_Id = '$order_id'
    ");
    $order = mysqli_fetch_assoc($orderRes);

    if (!$order) {
        throw new Exception("Order not found");
    }

    $totalAmount = $order['Total_Amount'];

    /* Insert payment */
    mysqli_query($connection, "
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        VALUES
        ('$order_id', CURDATE(), 'Razorpay', '$totalAmount', 'SUCCESS', '$razorpay_payment_id')
    ");

    /* Confirm order */
    mysqli_query($connection, "
        UPDATE `order` SET Status = 'CONFIRM' WHERE Order_Id = '$order_id'
    ");
    /* Deduct stock AFTER successful payment */
$orderItems = mysqli_query($connection, "
    SELECT Product_Id, Quantity
    FROM order_item
    WHERE Order_Id = '$order_id'
");

while ($item = mysqli_fetch_assoc($orderItems)) {

    $productId = (int)$item['Product_Id'];
    $qty       = (int)$item['Quantity'];

    // Deduct stock safely
    $updateStock = mysqli_query($connection, "
        UPDATE stock_details
        SET Stock_Available = Stock_Available - $qty,
            Last_Update = CURDATE()
        WHERE Product_Id = '$productId'
          AND Stock_Available >= $qty
    ");
    if (mysqli_affected_rows($connection) === 0) {
        throw new Exception("Insufficient stock for product ID $productId");
    }
}

    /* Clear cart */
    mysqli_query($connection, "
        DELETE FROM customize_cart_details
        WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
    ");
    mysqli_query($connection, "
        DELETE FROM cart WHERE User_Id = '$user_id'
    ");

    /* Clear session */
    unset($_SESSION['pending_order_id'], $_SESSION['total'], $_SESSION['subtotal']);

    mysqli_commit($connection);

    echo json_encode(["success" => true, "order_id" => $order_id]);

} catch (Exception $e) {

    mysqli_rollback($connection);
    echo json_encode(["success" => false, "error" => "Payment failed"]);
}

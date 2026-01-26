<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['pending_order_id']) || !isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "No pending order"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $_SESSION['pending_order_id'];
$razorpay_payment_id = $data['razorpay_payment_id'] ?? "";
$user_id = $_SESSION['User_Id'];

/* ✅ Get order total from DB (NOT recalculated) */
$orderQuery = mysqli_query($connection, "
    SELECT Total_Amount FROM `order` WHERE Order_Id = '$order_id'
");
$orderData = mysqli_fetch_assoc($orderQuery);
$totalAmount = $orderData['Total_Amount'];

/* ✅ Insert order items */
$cartItems = mysqli_query($connection, "
    SELECT * FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");

while ($item = mysqli_fetch_assoc($cartItems)) {

    $product_id = $item['Product_Id'];
    $qty = $item['Quantity'];
    $price = $item['Price'];

    mysqli_query($connection, "
        INSERT INTO order_item (Order_Id, Product_Id, Quantity, Price_Snapshot)
        VALUES ('$order_id', '$product_id', '$qty', '$price')
    ");
}

/* ✅ Insert payment details */
mysqli_query($connection, "
    INSERT INTO payment_details (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
    VALUES ('$order_id', CURDATE(), 'Razorpay', '$totalAmount', 'SUCCESS', '$razorpay_payment_id')
");

/* ✅ Update order status */
mysqli_query($connection, "
    UPDATE `order` SET Status = 'CONFIRM' WHERE Order_Id = '$order_id'
");

/* ✅ Clear cart */
mysqli_query($connection, "
    DELETE FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");
mysqli_query($connection, "DELETE FROM cart WHERE User_Id = '$user_id'");

/* ✅ Clear session */
unset($_SESSION['pending_order_id']);
unset($_SESSION['total']);
unset($_SESSION['subtotal']);

echo json_encode(["success" => true, "order_id" => $order_id]);

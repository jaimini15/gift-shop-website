<?php
session_start();
include("../AdminPanel/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "No data received"]);
    exit;
}

if (!isset($_SESSION['BUY_NOW'])) {
    echo json_encode(["success" => false, "error" => "BUY_NOW session missing"]);
    exit;
}

$razorpay_payment_id = $data['razorpay_payment_id'];
$order_id = $_SESSION['BUY_NOW']['order_id'];
$buy = $_SESSION['BUY_NOW'];

// ✅ Get total amount from order table
$orderQuery = mysqli_query($connection, "SELECT Total_Amount FROM `order` WHERE Order_Id='$order_id'");
$orderData = mysqli_fetch_assoc($orderQuery);
$totalAmount = $orderData['Total_Amount'];

// ✅ Insert order_item
$sql = "
INSERT INTO order_item 
(Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image, Gift_Wrapping, Personalized_Message)
VALUES (
    '$order_id',
    '{$buy['product_id']}',
    '{$buy['qty']}',
    '$totalAmount',
    '".mysqli_real_escape_string($connection, $buy['custom_text'])."',
    '{$buy['custom_image']}',
    '{$buy['gift_wrap']}',
    '".mysqli_real_escape_string($connection, $buy['gift_msg'])."'
)";

if (!mysqli_query($connection, $sql)) {
    echo json_encode(["success" => false, "error" => mysqli_error($connection)]);
    exit;
}

// ✅ Insert payment_details
$paySql = "
INSERT INTO payment_details (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
VALUES ('$order_id', CURDATE(), 'Razorpay', '$totalAmount', 'PAID', '$razorpay_payment_id')
";

if (!mysqli_query($connection, $paySql)) {
    echo json_encode(["success" => false, "error" => mysqli_error($connection)]);
    exit;
}

// ✅ Update order status
mysqli_query($connection, "UPDATE `order` SET Status='CONFIRM' WHERE Order_Id='$order_id'");

// ✅ Clear session
unset($_SESSION['BUY_NOW']);

echo json_encode(["success" => true, "order_id" => $order_id]);
?>

<?php
session_start();
include("../AdminPanel/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit;
}

$order_id = $data['order_id'];
$razorpay_payment_id = $data['razorpay_payment_id'];

$user_id = $_SESSION['User_Id'];

// 1️⃣ Get order total amount
$orderQuery = mysqli_query($connection, "SELECT Total_Amount FROM `order` WHERE Order_Id = '$order_id'");
$orderData = mysqli_fetch_assoc($orderQuery);
$totalAmount = $orderData['Total_Amount'];

// 2️⃣ Insert into payment_details
$paymentInsert = mysqli_query($connection, "
    INSERT INTO payment_details (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
    VALUES ('$order_id', CURDATE(), 'Razorpay', '$totalAmount', 'PAID', '$razorpay_payment_id')
");

// 3️⃣ Insert into order_item from customize_cart_details
$cartItems = mysqli_query($connection, "
    SELECT * FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");

while ($item = mysqli_fetch_assoc($cartItems)) {

    $product_id = $item['Product_Id'];
    $qty = $item['Quantity'];
    $price = $item['Price'];
    $custom_text = $item['Custom_Text'];
    $custom_image = $item['Custom_Image'];
    $gift_wrap = $item['Gift_Wrapping'];
    $message = $item['Personalized_Message'];

    mysqli_query($connection, "
        INSERT INTO order_item (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image, Gift_Wrapping, Personalized_Message)
        VALUES ('$order_id', '$product_id', '$qty', '$price', '$custom_text', '$custom_image', '$gift_wrap', '$message')
    ");
}

// 4️⃣ Update order status to CONFIRM
mysqli_query($connection, "
    UPDATE `order` SET Status = 'CONFIRM' WHERE Order_Id = '$order_id'
");

// 5️⃣ Delete cart data
mysqli_query($connection, "
    DELETE FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");

mysqli_query($connection, "DELETE FROM cart WHERE User_Id = '$user_id'");

// 6️⃣ Success response
echo json_encode(["success" => true, "order_id" => $order_id]);
?>

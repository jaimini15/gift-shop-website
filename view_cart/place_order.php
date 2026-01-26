<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['pending_order_id'])) {
    echo json_encode(["success" => false, "message" => "No pending order"]);
    exit;
}

$order_id = $_SESSION['pending_order_id'];
$user_id = $_SESSION['User_Id'];

$data = json_decode(file_get_contents("php://input"), true);
$razorpay_payment_id = $data['razorpay_payment_id'] ?? "";

// 1️⃣ Get order total
$orderQuery = mysqli_query($connection, "SELECT Total_Amount FROM `order` WHERE Order_Id = '$order_id'");
$orderData = mysqli_fetch_assoc($orderQuery);
$totalAmount = $orderData['Total_Amount'];
// ✅ Prevent duplicate payment entry
$checkPay = mysqli_query($connection, "
    SELECT Payment_Id FROM payment_details WHERE Order_Id = '$order_id'
");

if (mysqli_num_rows($checkPay) > 0) {
    echo json_encode([
        "success" => true,
        "message" => "Payment already recorded",
        "order_id" => $order_id
    ]);
    exit;
}
// ✅ Insert payment with correct amount
mysqli_query($connection, "
    INSERT INTO payment_details (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
    VALUES ('$order_id', CURDATE(), 'Razorpay', '$newTotal', 'PAID', '$razorpay_payment_id')
");

// 3️⃣ Insert order items
$cartItems = mysqli_query($connection, "
    SELECT * FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");
$checkItems = mysqli_query($connection, "
    SELECT Item_Id FROM order_item WHERE Order_Id = '$order_id'
");

if (mysqli_num_rows($checkItems) > 0) {
    echo json_encode([
        "success" => true,
        "message" => "Order items already inserted",
        "order_id" => $order_id
    ]);
    exit;
}

while ($item = mysqli_fetch_assoc($cartItems)) {

    mysqli_query($connection, "
        INSERT INTO order_item (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image, Gift_Wrapping, Personalized_Message)
        VALUES (
            '$order_id',
            '{$item['Product_Id']}',
            '{$item['Quantity']}',
            '{$item['Price']}',
            '{$item['Custom_Text']}',
            '{$item['Custom_Image']}',
            '{$item['Gift_Wrapping']}',
            '{$item['Personalized_Message']}'
        )
    ");
}
// ✅ Recalculate order total from order_item
$totalQuery = mysqli_query($connection, "
    SELECT SUM(Quantity * Price_Snapshot) AS total 
    FROM order_item 
    WHERE Order_Id = '$order_id'
");

$totalRow = mysqli_fetch_assoc($totalQuery);
$newTotal = $totalRow['total'] ?? 0;

// ✅ Update order total
mysqli_query($connection, "
    UPDATE `order` 
    SET Total_Amount = '$newTotal' 
    WHERE Order_Id = '$order_id'
");


// 4️⃣ Update order status (IMPORTANT)
mysqli_query($connection, "
    UPDATE `order` SET Status = 'CONFIRM' WHERE Order_Id = '$order_id'
");

// 5️⃣ Clear cart
mysqli_query($connection, "
    DELETE FROM customize_cart_details 
    WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id = '$user_id')
");
mysqli_query($connection, "DELETE FROM cart WHERE User_Id = '$user_id'");

// 6️⃣ Clear session (NOW correct place)
unset($_SESSION['pending_order_id']);
unset($_SESSION['subtotal']);
unset($_SESSION['total']);

echo json_encode(["success" => true, "order_id" => $order_id]);

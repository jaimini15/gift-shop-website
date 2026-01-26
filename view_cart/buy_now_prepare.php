<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$user_id = $_SESSION['User_Id'];

$product_id = $_POST['product_id'];
$custom_text = $_POST['custom_text'] ?? '';
$gift_wrap = $_POST['gift_wrap'] ?? 0;
$gift_card = $_POST['gift_card'] ?? 0;
$gift_msg = $_POST['gift_card_msg'] ?? '';

$imagePath = null;

// Upload image if exists
if (!empty($_FILES['custom_image']['name'])) {
    $folder = "../uploads/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $fileName = time() . "_" . basename($_FILES['custom_image']['name']);
    $target = $folder . $fileName;

    if (move_uploaded_file($_FILES['custom_image']['tmp_name'], $target)) {
        $imagePath = $fileName;
    }
}

// Get product price
$pq = mysqli_query($connection, "SELECT Price FROM Product_Details WHERE Product_Id='$product_id'");
$pdata = mysqli_fetch_assoc($pq);
$price = $pdata['Price'];

$total = $price + ($gift_wrap ? 39 : 0) + ($gift_card ? 50 : 0);

// ✅ Create order with PENDING status
mysqli_query($connection, "
    INSERT INTO `order` (User_Id, Total_Amount, Status)
    VALUES ('$user_id', '$total', 'PENDING')
");

$order_id = mysqli_insert_id($connection);

// ✅ Store buy now data in SESSION (important)
$_SESSION['BUY_NOW'] = [
    'order_id' => $order_id,
    'product_id' => $product_id,
    'qty' => 1,
    'price' => $price,
    'custom_text' => $custom_text,
    'custom_image' => $imagePath,
    'gift_wrap' => $gift_wrap,
    'gift_msg' => $gift_msg
];

// ✅ RETURN JSON (NO REDIRECT)
echo json_encode([
    "success" => true,
    "order_id" => $order_id
]);
exit;

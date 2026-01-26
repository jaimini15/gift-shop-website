<?php
session_start();
header("Content-Type: application/json");

include("../AdminPanel/db.php");


/* Load Razorpay keys */
$config = require __DIR__ . "/../config/razorpay.php";
$keyId     = $config['key_id'];
$keySecret = $config['key_secret'];

/* ✅ Check BUY NOW session */
if (!isset($_SESSION['BUY_NOW'])) {
    echo json_encode(["success" => false, "error" => "BUY NOW session missing"]);
    exit;
}

$orderId = $_SESSION['BUY_NOW']['order_id'];

/* ✅ Get total amount from order table */
$q = mysqli_query($connection, "SELECT Total_Amount FROM `order` WHERE Order_Id='$orderId'");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    echo json_encode(["success" => false, "error" => "Order not found"]);
    exit;
}

$total = $row['Total_Amount'];
$amount = (int) round($total * 100); // paise

/* ✅ Razorpay payload */
$payload = [
    "amount" => $amount,
    "currency" => "INR",
    "receipt" => "BUYNOW_" . $orderId,
    "payment_capture" => 1
];

/* ✅ Create Razorpay order */
$ch = curl_init("https://api.razorpay.com/v1/orders");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_USERPWD => $keyId . ":" . $keySecret,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (empty($data['id'])) {
    echo json_encode(["success" => false, "error" => "Razorpay order failed"]);
    exit;
}

$_SESSION['razorpay_order_id'] = $data['id'];

/* ✅ Response */
echo json_encode([
    "success" => true,
    "key" => $keyId,
    "orderId" => $data['id'],
    "amount" => $amount
]);



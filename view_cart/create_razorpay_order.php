<?php
session_start();
header("Content-Type: application/json");

/* Load Razorpay keys */
$config = require __DIR__ . "/../config/razorpay.php";
$keyId     = $config['key_id'];
$keySecret = $config['key_secret'];

/* Check session */
if (!isset($_SESSION['pending_order_id']) || !isset($_SESSION['total'])) {
    echo json_encode(["success" => false, "error" => "Session missing"]);
    exit;
}

$total = $_SESSION['total'];
$amount = (int) round($total * 100); // paise

$payload = [
    "amount" => $amount,
    "currency" => "INR",
    "receipt" => "ORDER_" . $_SESSION['pending_order_id'],
    "payment_capture" => 1
];

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

echo json_encode([
    "success" => true,
    "key" => $keyId,
    "orderId" => $data['id'],
    "amount" => $amount
]);

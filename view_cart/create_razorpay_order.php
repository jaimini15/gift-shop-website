<?php
session_start();
header("Content-Type: application/json");

$keyId     = "rzp_test_S6xjDy1mlK1WMx";
$keySecret = "dDkvmQ0MUEern9u7VJrAbm3s";

if (!isset($_SESSION['total'], $_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Session missing"
    ]);
    exit;
}

$amount = (int)($_SESSION['total'] * 100); // paise

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

if ($response === false) {
    echo json_encode([
        "success" => false,
        "error" => curl_error($ch)
    ]);
    exit;
}

$data = json_decode($response, true);

if (!isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Razorpay order creation failed",
        "raw" => $data
    ]);
    exit;
}

$_SESSION['razorpay_order_id'] = $data['id'];

echo json_encode([
    "success" => true,
    "key" => $keyId,
    "orderId" => $data['id'],
    "amount" => $amount
]);

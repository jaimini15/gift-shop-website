<?php
session_start();

// $_SESSION['pending_order_id'] = 123;
// $_SESSION['total'] = 199;

header("Content-Type: application/json");

/* 🔐 Load Razorpay Keys */
$config = require __DIR__ . "/../config/razorpay.php";
$keyId     = $config['key_id'];
$keySecret = $config['key_secret'];

/* 🛑 Validate session */
if (
    !isset($_SESSION['pending_order_id']) ||
    (!isset($_SESSION['total']) && !isset($_SESSION['subtotal']))
) {
    echo json_encode([
        "success" => false,
        "error" => "Session missing"
    ]);
    exit;
}

/* 💰 Amount handling */
$total = $_SESSION['total'] ?? $_SESSION['subtotal'];
$amount = (int) round($total * 100); // paise

/* 📦 Razorpay Order Payload */
$payload = [
    "amount" => $amount,
    "currency" => "INR",
    "receipt" => "ORDER_" . $_SESSION['pending_order_id'],
    "payment_capture" => 1
];

/* 🌐 cURL Request */
$ch = curl_init("https://api.razorpay.com/v1/orders");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_USERPWD => $keyId . ":" . $keySecret,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_SSL_VERIFYPEER => true, // IMPORTANT for localhost
    CURLOPT_TIMEOUT => 30
]);
if (empty($keyId) || empty($keySecret)) {
    echo json_encode([
        "success" => false,
        "error" => "Razorpay keys missing",
        "keyId" => $keyId
    ]);
    exit;
}

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo json_encode([
        "success" => false,
        "error" => curl_error($ch)
    ]);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

/* ❌ Razorpay error */
if ($httpStatus !== 200 || empty($data['id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Razorpay order creation failed",
        "razorpay" => $data
    ]);
    exit;
}

/* ✅ Save order ID */
$_SESSION['razorpay_order_id'] = $data['id'];

/* ✅ Success */
echo json_encode([
    "success" => true,
    "key" => $keyId,       // public key only
    "orderId" => $data['id'],
    "amount" => $amount
]);

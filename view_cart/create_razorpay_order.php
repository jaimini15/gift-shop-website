<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . '/../vendor/autoload.php';

use Razorpay\Api\Api;

session_start();

include("../AdminPanel/db.php");
$config = require __DIR__ . '/../config/razorpay.php';

if (!isset($_SESSION['pending_order_id'], $_SESSION['User_Id'])) {
    echo json_encode([
        "success" => false,
        "error" => "No pending order"
    ]);
    exit;
}

$orderId = (int) $_SESSION['pending_order_id'];

$res = mysqli_query($connection, "
    SELECT Total_Amount 
    FROM `order` 
    WHERE Order_Id = $orderId
");

$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo json_encode([
        "success" => false,
        "error" => "Order not found in DB"
    ]);
    exit;
}

$amount = (int) ($row['Total_Amount'] * 100); // paise



$api = new Api($config['key_id'], $config['key_secret']);

$razorpayOrder = $api->order->create([
    'receipt'  => "order_$orderId",
    'amount'   => $amount,
    'currency' => 'INR'
]);

echo json_encode([
    "success" => true,
    "key"     => $config['key_id'],
    "amount"  => $amount,
    "orderId" => $razorpayOrder['id']
]);
exit;

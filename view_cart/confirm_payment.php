<?php
session_start();
header("Content-Type: application/json");

include("../AdminPanel/db.php");
require("../vendor/autoload.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$config = require __DIR__ . "/../config/razorpay.php";

$keyId     = $config['key_id'];
$keySecret = $config['key_secret'];


$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['razorpay_payment_id']) ||
    empty($data['razorpay_signature']) ||
    empty($_SESSION['razorpay_order_id']) ||
    empty($_SESSION['pending_order_id'])
) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$api = new Api($keyId, $keySecret);

try {

    // ✅ VERIFY SIGNATURE
    $api->utility->verifyPaymentSignature([
        "razorpay_order_id"    => $_SESSION['razorpay_order_id'],
        "razorpay_payment_id" => $data['razorpay_payment_id'],
        "razorpay_signature"  => $data['razorpay_signature']
    ]);

    $orderId   = (int)$_SESSION['pending_order_id'];
    $paymentId = mysqli_real_escape_string($connection, $data['razorpay_payment_id']);

    // ✅ UPDATE ORDER
    $result = mysqli_query($connection, "
        UPDATE orders 
        SET 
            payment_status = 'SUCCESS',
            payment_id     = '$paymentId',
            payment_method = 'RAZORPAY'
        WHERE Order_Id = $orderId
    ");

    if (!$result) {
        echo json_encode(["success" => false, "error" => "DB update failed"]);
        exit;
    }

    // ✅ CLEAR SESSION
    unset($_SESSION['pending_order_id']);
    unset($_SESSION['razorpay_order_id']);

    echo json_encode([
        "success"  => true,
        "order_id"=> $orderId
    ]);

} catch (SignatureVerificationError $e) {

    echo json_encode([
        "success" => false,
        "error"   => "Signature mismatch"
    ]);
}

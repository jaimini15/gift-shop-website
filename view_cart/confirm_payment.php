<?php
session_start();
include("../AdminPanel/db.php");
require("../vendor/autoload.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$keyId     = "rzp_test_xxxxxxxxxx";
$keySecret = "xxxxxxxxxxxxxxxx";

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['razorpay_payment_id']) ||
    empty($data['razorpay_signature']) ||
    empty($_SESSION['razorpay_order_id']) ||
    empty($_SESSION['pending_order_id'])
) {
    echo json_encode(["success" => false]);
    exit;
}

$api = new Api($keyId, $keySecret);

try {
    $api->utility->verifyPaymentSignature([
        "razorpay_order_id"   => $_SESSION['razorpay_order_id'],
        "razorpay_payment_id"=> $data['razorpay_payment_id'],
        "razorpay_signature" => $data['razorpay_signature']
    ]);

    $orderId = (int)$_SESSION['pending_order_id'];

    mysqli_query($connection, "
        UPDATE orders 
        SET payment_status='SUCCESS',
            payment_id='{$data['razorpay_payment_id']}',
            payment_method='RAZORPAY'
        WHERE Order_Id=$orderId
    ");

    echo json_encode([
        "success" => true,
        "order_id" => $orderId
    ]);

} catch (SignatureVerificationError $e) {

    echo json_encode([
        "success" => false,
        "error" => "Signature mismatch"
    ]);
}

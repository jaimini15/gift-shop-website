<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'], $_SESSION['pending_order_id'])) {
    echo json_encode(["success"=>false,"error"=>"Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['payment_method'])) {
    echo json_encode(["success"=>false,"error"=>"Payment method missing"]);
    exit;
}

$userId  = $_SESSION['User_Id'];
$orderId = $_SESSION['pending_order_id'];
$method  = $data['payment_method'];

mysqli_begin_transaction($connection);

try {
    mysqli_query($connection,"
        UPDATE `order`
        SET Status='CONFIRM'
        WHERE Order_Id=$orderId AND User_Id=$userId
    ");

    $txn = "TXN".time().rand(1000,9999);

    mysqli_query($connection,"
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        SELECT Order_Id, NOW(), '$method', Total_Amount, 'SUCCESS', '$txn'
        FROM `order`
        WHERE Order_Id=$orderId
    ");

    unset($_SESSION['pending_order_id']);
    unset($_SESSION['hamper_selected']);
    unset($_SESSION['buy_now']);

    mysqli_commit($connection);

    echo json_encode(["success"=>true,"order_id"=>$orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success"=>false,"error"=>"Payment failed"]);
}

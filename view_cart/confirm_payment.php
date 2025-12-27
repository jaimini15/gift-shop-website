<?php
session_start();
include("../AdminPanel/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['User_Id'], $_SESSION['total'])) {
    echo json_encode(["success"=>false,"error"=>"Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['order_id']) || empty($data['payment_method'])) {
    echo json_encode(["success"=>false,"error"=>"Invalid request"]);
    exit;
}

$orderId = (int)$data['order_id'];
$method  = $data['payment_method'];
$amount  = $_SESSION['total'];
$userId  = $_SESSION['User_Id'];

$check = mysqli_prepare($connection,"
    SELECT Order_Id FROM `order`
    WHERE Order_Id=? AND User_Id=? AND Status='PENDING'
");
mysqli_stmt_bind_param($check,"ii",$orderId,$userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    echo json_encode(["success"=>false,"error"=>"Order not found or expired"]);
    exit;
}

mysqli_begin_transaction($connection);

try {

    mysqli_prepare($connection,"
        UPDATE `order` SET Status='CONFIRMED' WHERE Order_Id=?
    ")->bind_param("i",$orderId)->execute();

    $txn = "TXN".time().rand(1000,9999);

    mysqli_prepare($connection,"
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        VALUES (?, NOW(), ?, ?, 'SUCCESS', ?)
    ")->bind_param("isds",$orderId,$method,$amount,$txn)->execute();

    mysqli_query($connection,"
        DELETE FROM customize_cart_details
        WHERE Cart_Id IN (SELECT Cart_Id FROM cart WHERE User_Id='$userId')
    ");
    mysqli_query($connection,"DELETE FROM cart WHERE User_Id='$userId'");

    mysqli_commit($connection);

    echo json_encode(["success"=>true,"order_id"=>$orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success"=>false,"error"=>$e->getMessage()]);
}

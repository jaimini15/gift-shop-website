<?php
session_start();
include("../AdminPanel/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['User_Id'], $data['order_id'])) {
    echo json_encode(["success"=>false]);
    exit;
}

$orderId = (int)$data['order_id'];
$userId  = (int)$_SESSION['User_Id'];

mysqli_begin_transaction($connection);

try {

    $check = mysqli_prepare($connection,"
        SELECT Order_Id FROM `order`
        WHERE Order_Id=? AND User_Id=? AND Status='PENDING'
    ");
    mysqli_stmt_bind_param($check,"ii",$orderId,$userId);
    mysqli_stmt_execute($check);

    if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {

        mysqli_prepare($connection,"
            DELETE FROM order_item WHERE Order_Id=?
        ")->bind_param("i",$orderId)->execute();

        mysqli_prepare($connection,"
            DELETE FROM `order` WHERE Order_Id=?
        ")->bind_param("i",$orderId)->execute();
    }

    unset($_SESSION['pending_order_id']);
    mysqli_commit($connection);

    echo json_encode(["success"=>true]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success"=>false]);
}

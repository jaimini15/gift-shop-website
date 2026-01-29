<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

/*  AUTH CHECK*/
if (!isset($_SESSION['User_Id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Login required"
    ]);
    exit;
}

if (!isset($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => true
    ]);
    exit;
}


$userId  = $_SESSION['User_Id'];
$orderId = $_SESSION['pending_order_id'];

/* START TRANSACTION */
mysqli_begin_transaction($connection);

try {

    /* DELETE ORDER ITEMS (ONLY IF ORDER IS PENDING)*/
    $stmt = mysqli_prepare(
        $connection,
        "DELETE oi FROM order_item oi
         JOIN `order` o ON o.Order_Id = oi.Order_Id
         WHERE oi.Order_Id = ? AND o.User_Id = ? AND o.Status = 'PENDING'"
    );
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);

    /* DELETE ORDER*/
    $stmt = mysqli_prepare(
        $connection,
        "DELETE FROM `order`
         WHERE Order_Id = ? AND User_Id = ? AND Status = 'PENDING'"
    );
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception("Pending order not found or already processed");
    }
    unset($_SESSION['pending_order_id']);
    mysqli_commit($connection);

    echo json_encode([
        "success" => true
    ]);

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "error"   => $e->getMessage()
    ]);
}

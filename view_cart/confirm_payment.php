<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

/* =======================
   READ JSON INPUT
======================= */
$data = json_decode(file_get_contents("php://input"), true);

/* =======================
   AUTH & SESSION CHECK
======================= */
if (
    !isset($_SESSION['User_Id']) ||
    !isset($_SESSION['pending_order_id'])
) {
    echo json_encode([
        "success" => false,
        "error"   => "Unauthorized access"
    ]);
    exit;
}

if (empty($data['payment_method'])) {
    echo json_encode([
        "success" => false,
        "error"   => "Payment method missing"
    ]);
    exit;
}

$userId  = $_SESSION['User_Id'];
$orderId = $_SESSION['pending_order_id'];
$method  = trim($data['payment_method']);

/* =======================
   FETCH ORDER TOTAL (DB SOURCE OF TRUTH)
======================= */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Total_Amount
     FROM `order`
     WHERE Order_Id = ? AND User_Id = ? AND Status = 'PENDING'"
);
mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        "success" => false,
        "error"   => "Order not found or already processed"
    ]);
    exit;
}

$amount = (float) $row['Total_Amount'];

/* =======================
   BEGIN TRANSACTION
======================= */
mysqli_begin_transaction($connection);

try {

    /* =======================
       CONFIRM ORDER
    ======================= */
    $stmt = mysqli_prepare(
        $connection,
        "UPDATE `order`
         SET Status = 'CONFIRM'
         WHERE Order_Id = ? AND User_Id = ? AND Status = 'PENDING'"
    );
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception("Order confirmation failed");
    }

    /* =======================
       INSERT PAYMENT RECORD
    ======================= */
    $transactionId = "TXN" . date("YmdHis") . rand(1000, 9999);

    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        VALUES (?, NOW(), ?, ?, 'SUCCESS', ?)"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "isds",
        $orderId,
        $method,
        $amount,
        $transactionId
    );
    mysqli_stmt_execute($stmt);

    /* =======================
       CLEAR USER CART
    ======================= */
    $stmt = mysqli_prepare(
        $connection,
        "DELETE FROM customize_cart_details
         WHERE Cart_Id IN (
            SELECT Cart_Id FROM cart WHERE User_Id = ?
         )"
    );
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare(
        $connection,
        "DELETE FROM cart WHERE User_Id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    /* =======================
       CLEANUP & COMMIT
    ======================= */
    unset($_SESSION['pending_order_id']);
    mysqli_commit($connection);

    echo json_encode([
        "success"  => true,
        "order_id"=> $orderId
    ]);

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "error"   => $e->getMessage()
    ]);
}

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

include("../AdminPanel/db.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ===== READ INPUT ===== */
$data = json_decode(file_get_contents("php://input"), true);

$orderId = (int)($data['order_id'] ?? 0);
$paymentMethod = trim($data['payment_method'] ?? '');

if (!$orderId || !$paymentMethod) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

/* ===== AUTH ===== */
$userId = (int)($_SESSION['User_Id'] ?? 0);
if (!$userId) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

try {
    mysqli_begin_transaction($connection);

    /* ===== 1. CONFIRM ORDER ===== */
    $stmt = mysqli_prepare(
        $connection,
        "UPDATE `order`
         SET Status='CONFIRMED'
         WHERE Order_Id=? AND User_Id=? AND Status='PENDING'"
    );
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        throw new Exception("Order already processed or invalid");
    }

    /* ===== 2. UPDATE PAYMENT (NOT INSERT) ===== */
    $txnRef = "TXN" . time() . random_int(1000, 9999);

   $stmt = mysqli_prepare(
    $connection,
    "INSERT INTO payment_details
     (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
     SELECT Order_Id, CURDATE(), ?, Total_Amount, 'SUCCESS', ?
     FROM `order`
     WHERE Order_Id=?"
);

    mysqli_stmt_bind_param($stmt, "ssi", $paymentMethod, $txnRef, $orderId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        throw new Exception("Payment record invalid or already updated");
    }

    /* ===== 3. CLEAR SESSION ===== */
    unset(
        $_SESSION['pending_order_id'],
        $_SESSION['hamper_selected'],
        $_SESSION['buy_now'],
        $_SESSION['buy_now_product_id'],
        $_SESSION['buy_now_gift_wrap'],
        $_SESSION['buy_now_gift_card']
    );

    mysqli_commit($connection);

    echo json_encode([
        "success" => true,
        "order_id" => $orderId
    ]);

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

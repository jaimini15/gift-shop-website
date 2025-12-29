<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

/* =======================
   AUTH CHECK
======================= */
if (!isset($_SESSION['User_Id'])) {
    echo json_encode([
        "success" => false,
        "error"   => "Login required"
    ]);
    exit;
}

$userId         = $_SESSION['User_Id'];
$hamperSelected = $_SESSION['hamper_selected'] ?? 0;

/* =======================
   READ JSON INPUT
======================= */
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['payment_method'])) {
    echo json_encode([
        "success" => false,
        "error"   => "Payment method required"
    ]);
    exit;
}

$paymentMethod = trim($data['payment_method']);

/* =======================
   PREVENT MULTIPLE PENDING ORDERS
======================= */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Order_Id
     FROM `order`
     WHERE User_Id = ? AND Status = 'PENDING'
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($res)) {
    echo json_encode([
        "success" => false,
        "pending" => true,
        "message" => "You already have a pending order"
    ]);
    exit;
}

/* =======================
   GET CART ID
======================= */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Cart_Id FROM cart WHERE User_Id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$cart = mysqli_fetch_assoc($res);
if (!$cart) {
    echo json_encode([
        "success" => false,
        "error"   => "Cart not found"
    ]);
    exit;
}

$cartId = $cart['Cart_Id'];

/* =======================
   CALCULATE TOTAL (DB SOURCE)
======================= */
$stmt = mysqli_prepare(
    $connection,
    "SELECT SUM(Quantity * Price) AS total
     FROM customize_cart_details
     WHERE Cart_Id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $cartId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($res);
$totalAmount = (float) ($row['total'] ?? 0);

if ($totalAmount <= 0) {
    echo json_encode([
        "success" => false,
        "error"   => "Cart is empty"
    ]);
    exit;
}

/* =======================
   START TRANSACTION
======================= */
mysqli_begin_transaction($connection);

try {

    /* =======================
       INSERT ORDER (PENDING)
    ======================= */
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO `order` (User_Id, Total_Amount, Status)
         VALUES (?, ?, 'PENDING')"
    );
    mysqli_stmt_bind_param($stmt, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmt);

    $orderId = mysqli_insert_id($connection);

    if ($orderId <= 0) {
        throw new Exception("Order insert failed");
    }

    /* =======================
       INSERT ORDER ITEMS
    ======================= */
    $stmtItems = mysqli_prepare(
        $connection,
        "SELECT Product_Id, Quantity, Price, Custom_Text, Custom_Image
         FROM customize_cart_details
         WHERE Cart_Id = ?"
    );
    mysqli_stmt_bind_param($stmtItems, "i", $cartId);
    mysqli_stmt_execute($stmtItems);
    $itemsRes = mysqli_stmt_get_result($stmtItems);

    $stmtInsertItem = mysqli_prepare(
        $connection,
        "INSERT INTO order_item
        (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image, Is_Hamper_Suggested)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    while ($item = mysqli_fetch_assoc($itemsRes)) {
        mysqli_stmt_bind_param(
            $stmtInsertItem,
            "iiidssi",
            $orderId,
            $item['Product_Id'],
            $item['Quantity'],
            $item['Price'],
            $item['Custom_Text'],
            $item['Custom_Image'],
            $hamperSelected
        );
        mysqli_stmt_execute($stmtInsertItem);
    }

    /* =======================
       COMMIT & SESSION
    ======================= */
    mysqli_commit($connection);

    // ✅ SET pending order ID ONCE
    $_SESSION['pending_order_id'] = $orderId;

    echo json_encode([
        "success"  => true,
        "order_id" => $orderId
    ]);

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "error"   => "Order creation failed"
    ]);
}

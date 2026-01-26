<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "Login required"]);
    exit;
}

$userId = (int)$_SESSION['User_Id'];

/* ✅ Prevent duplicate order */
if (isset($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => true,
        "order_id" => $_SESSION['pending_order_id']
    ]);
    exit;
}

/* ✅ USE SESSION TOTAL (FROM CART PAGE) */
$totalAmount = $_SESSION['total'] ?? 0;

if ($totalAmount <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid total"]);
    exit;
}

/* ✅ Create order */
$stmt = $connection->prepare(
    "INSERT INTO `order` (User_Id, Total_Amount, Status)
     VALUES (?, ?, 'PENDING')"
);
$stmt->bind_param("id", $userId, $totalAmount);
$stmt->execute();

$orderId = $stmt->insert_id;

/* ✅ Store order id */
$_SESSION['pending_order_id'] = $orderId;

echo json_encode([
    "success" => true,
    "order_id" => $orderId,
    "amount" => $totalAmount
]);

<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "Login required"]);
    exit;
}

$userId = $_SESSION['User_Id'];
$totalAmount = $_SESSION['total'] ?? 0;

if ($totalAmount <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid total"]);
    exit;
}

// ✅ If order already exists, don't create again
if (isset($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => true,
        "order_id" => $_SESSION['pending_order_id'],
        "pending" => true
    ]);
    exit;
}

// ✅ Create PENDING order ONLY ONCE
$stmt = $connection->prepare(
    "INSERT INTO `order` (User_Id, Total_Amount, Status)
     VALUES (?, ?, 'PENDING')"
);
$stmt->bind_param("id", $userId, $totalAmount);
$stmt->execute();

$orderId = $stmt->insert_id;

// store in session
$_SESSION['pending_order_id'] = $orderId;

echo json_encode([
    "success"  => true,
    "order_id" => $orderId
]);

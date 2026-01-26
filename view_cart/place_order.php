<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

/* ❌ Prevent duplicate pending orders */
if (isset($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => true,
        "order_id" => $_SESSION['pending_order_id']
    ]);
    exit;
}

/* ✅ Auth check */
if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "Login required"]);
    exit;
}

$userId = (int)$_SESSION['User_Id'];

/* ✅ Use frozen cart total */
$totalAmount = $_SESSION['total'] ?? 0;
if ($totalAmount <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid total"]);
    exit;
}

/* ======================
   START TRANSACTION
====================== */
mysqli_begin_transaction($connection);

try {

    /* ✅ 1. Create order */
    $stmt = $connection->prepare(
        "INSERT INTO `order` (User_Id, Total_Amount, Status)
         VALUES (?, ?, 'PENDING')"
    );
    $stmt->bind_param("id", $userId, $totalAmount);
    $stmt->execute();

    $orderId = $stmt->insert_id;

    if (!$orderId) {
        throw new Exception("Order creation failed");
    }

    /* ✅ 2. Insert order items (NOW orderId exists) */
    $cartItems = mysqli_query($connection, "
        SELECT Product_Id, Quantity, Price
        FROM customize_cart_details
        WHERE Cart_Id = (
            SELECT Cart_Id FROM cart WHERE User_Id = '$userId'
        )
    ");

    while ($item = mysqli_fetch_assoc($cartItems)) {
        mysqli_query($connection, "
            INSERT INTO order_item
            (Order_Id, Product_Id, Quantity, Price_Snapshot)
            VALUES (
                '$orderId',
                '{$item['Product_Id']}',
                '{$item['Quantity']}',
                '{$item['Price']}'
            )
        ");
    }

    /* ✅ 3. Save session */
    $_SESSION['pending_order_id'] = $orderId;

    mysqli_commit($connection);

    echo json_encode([
        "success" => true,
        "order_id" => $orderId,
        "amount" => $totalAmount
    ]);

} catch (Exception $e) {

    mysqli_rollback($connection);

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

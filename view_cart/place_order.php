<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

/* ================= AUTH ================= */

if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "Login required"]);
    exit;
}

$userId  = (int)$_SESSION['User_Id'];
$isBuyNow = !empty($_SESSION['buy_now']);

/* ================= INPUT ================= */

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['payment_method'])) {
    echo json_encode(["success" => false, "error" => "Payment method required"]);
    exit;
}

/* ================= CALCULATE TOTAL ================= */

$totalAmount = 0;

/* ===== BUY NOW FLOW ===== */
if ($isBuyNow) {

    $productId = $_SESSION['buy_now_product_id'] ?? 0;
    if (!$productId) {
        echo json_encode(["success" => false, "error" => "Invalid product"]);
        exit;
    }

    $stmt = $connection->prepare(
        "SELECT Price FROM product_details WHERE Product_Id = ?"
    );
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo json_encode(["success" => false, "error" => "Product not found"]);
        exit;
    }

    $totalAmount = (float)$product['Price'];

}
/* ===== CART FLOW ===== */
else {

    $stmt = $connection->prepare(
        "SELECT SUM(ccd.Quantity * ccd.Price) AS total
         FROM customize_cart_details ccd
         JOIN cart c ON c.Cart_Id = ccd.Cart_Id
         WHERE c.User_Id = ?"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (($row['total'] ?? 0) <= 0) {
        echo json_encode(["success" => false, "error" => "Cart empty"]);
        exit;
    }

    $totalAmount = (float)$row['total'];
}

/* ================= CREATE PENDING ORDER ONLY ================= */

$stmt = $connection->prepare(
    "INSERT INTO `order` (User_Id, Total_Amount, Status)
     VALUES (?, ?, 'PENDING')"
);
$stmt->bind_param("id", $userId, $totalAmount);
$stmt->execute();

$orderId = $stmt->insert_id;

/* Store pending order */
$_SESSION['pending_order_id'] = $orderId;

echo json_encode([
    "success"  => true,
    "order_id" => $orderId
]);

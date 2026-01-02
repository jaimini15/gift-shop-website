<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
/* ✅ ADD THIS BLOCK HERE */
$data = json_decode(file_get_contents("php://input"), true);

$payment_method = $data['payment_method'] ?? null;

if (!$payment_method) {
    echo json_encode([
        "success" => false,
        "error" => "Payment method missing"
    ]);
    exit;
}
/* ✅ END BLOCK */
include("../AdminPanel/db.php");


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ================= AUTH ================= */
if (empty($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "error" => "Login required"]);
    exit;
}

$userId = (int)$_SESSION['User_Id'];
$hamperSelected = (int)($_SESSION['hamper_selected'] ?? 0);

/* ================= HANDLE PENDING ORDER ================= */
if (!empty($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => false,
        "pending" => true,
        "order_id" => $_SESSION['pending_order_id']
    ]);
    exit;
}

/* ================= PAYMENT METHOD ================= */
$data = json_decode(file_get_contents("php://input"), true);
$paymentMethod = trim($data['payment_method'] ?? '');

if ($paymentMethod === '') {
    echo json_encode(["success" => false, "error" => "Payment method required"]);
    exit;
}

/* ================= BUY NOW FLOW ================= */
if (!empty($_SESSION['buy_now'])) {

    $item = $_SESSION['buy_now_item'] ?? null;
    if (!$item || empty($item['product_id'])) {
        echo json_encode(["success" => false, "error" => "Invalid Buy Now session"]);
        exit;
    }

    $productId = (int)$item['product_id'];

    /* Fetch base price */
    $stmt = mysqli_prepare(
        $connection,
        "SELECT Price FROM Product_Details WHERE Product_Id=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$product) {
        echo json_encode(["success" => false, "error" => "Product not found"]);
        exit;
    }

    $basePrice = (float)$product['Price'];

    /* Extras */
    $giftWrap = !empty($item['gift_wrap']) ? 39 : 0;
    $giftCard = !empty($item['gift_card']) ? 50 : 0;

    $priceSnapshot = $basePrice + $giftWrap + $giftCard;
    $totalAmount   = $priceSnapshot;

    $giftMessage = trim($item['gift_msg'] ?? '');

    mysqli_begin_transaction($connection);

    try {
        /* Insert order */
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO `order` (User_Id, Total_Amount, Status)
             VALUES (?, ?, 'PENDING')"
        );
        mysqli_stmt_bind_param($stmt, "id", $userId, $totalAmount);
        mysqli_stmt_execute($stmt);

        $orderId = mysqli_insert_id($connection);

        /* Insert order item */
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO order_item
            (Order_Id, Product_Id, Quantity, Price_Snapshot,
             Gift_Wrapping, Personalized_Message, Is_Hamper_Suggested)
             VALUES (?, ?, 1, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iidssi",
            $orderId,
            $productId,
            $priceSnapshot,
            $giftWrap ? 1 : 0,
            $giftMessage,
            $hamperSelected
        );

        mysqli_stmt_execute($stmt);

        mysqli_commit($connection);

        $_SESSION['pending_order_id'] = $orderId;

        echo json_encode(["success" => true, "order_id" => $orderId]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($connection);
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
        exit;
    }
}

/* ================= CART FLOW ================= */

/* Fetch cart */
$stmt = mysqli_prepare($connection, "SELECT Cart_Id FROM cart WHERE User_Id=?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$cart = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$cart) {
    echo json_encode(["success" => false, "error" => "Cart is empty"]);
    exit;
}

/* Fetch cart items (Price already includes extras) */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Product_Id, Quantity, Price, gift_wrap, Custom_Text
     FROM customize_cart_details
     WHERE Cart_Id=?"
);
mysqli_stmt_bind_param($stmt, "i", $cart['Cart_Id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$totalAmount = 0;
$items = [];

while ($row = mysqli_fetch_assoc($res)) {

    $price = (float)($row['Price'] ?? 0); // ✅ includes extras
    $qty   = (int)($row['Quantity'] ?? 1);

    if ($price <= 0 || $qty <= 0) continue;

    $totalAmount += ($price * $qty);

    $items[] = [
        'product_id' => (int)$row['Product_Id'],
        'qty'        => $qty,
        'price'      => $price,
        'gift_wrap'  => !empty($row['gift_wrap']) ? 1 : 0,
        'message'    => trim($row['Custom_Text'] ?? '')
    ];
}

if ($totalAmount <= 0 || empty($items)) {
    echo json_encode(["success" => false, "error" => "Invalid cart total"]);
    exit;
}

mysqli_begin_transaction($connection);

try {
    /* Insert order */
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO `order` (User_Id, Total_Amount, Status)
         VALUES (?, ?, 'PENDING')"
    );
    mysqli_stmt_bind_param($stmt, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmt);

    $orderId = mysqli_insert_id($connection);

    /* Insert order items */
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO order_item
        (Order_Id, Product_Id, Quantity, Price_Snapshot,
         Gift_Wrapping, Personalized_Message, Is_Hamper_Suggested)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    foreach ($items as $i) {
        mysqli_stmt_bind_param(
            $stmt,
            "iiidssi",
            $orderId,
            $i['product_id'],
            $i['qty'],
            $i['price'],
            $i['gift_wrap'],
            $i['message'],
            $hamperSelected
        );
        mysqli_stmt_execute($stmt);
    }

    mysqli_commit($connection);

    $_SESSION['pending_order_id'] = $orderId;

    echo json_encode(["success" => true, "order_id" => $orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

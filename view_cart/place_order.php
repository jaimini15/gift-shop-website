<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

include("../AdminPanel/db.php");

/* STRICT MYSQLI MODE */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ================= READ JSON INPUT ================= */
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

$paymentMethod = trim($data['payment_method'] ?? '');

/* ================= VALIDATION ================= */
if ($paymentMethod === '') {
    echo json_encode(["success" => false, "message" => "Payment method missing"]);
    exit;
}

if (empty($_SESSION['User_Id'])) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

$userId         = (int) $_SESSION['User_Id'];
$hamperSelected = (int) ($_SESSION['hamper_selected'] ?? 0);

/* ================= PREVENT DUPLICATE ORDER ================= */
if (!empty($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => false,
        "pending" => true,
        "order_id" => $_SESSION['pending_order_id']
    ]);
    exit;
}

/* =======================================================
   BUY NOW FLOW
   ======================================================= */
if (!empty($_SESSION['buy_now'])) {

    $productId = (int) ($_SESSION['buy_now_product_id'] ?? 0);
    if ($productId <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid Buy Now session"]);
        exit;
    }

    /* Fetch product price */
    $stmt = mysqli_prepare(
        $connection,
        "SELECT Price FROM Product_Details WHERE Product_Id=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$product) {
        echo json_encode(["success" => false, "message" => "Product not found"]);
        exit;
    }

    $basePrice  = (float) $product['Price'];
    $giftWrap   = (float) ($_SESSION['buy_now_gift_wrap'] ?? 0);
    $giftCard   = (float) ($_SESSION['buy_now_gift_card'] ?? 0);
    $totalAmount = $basePrice + $giftWrap + $giftCard;

    if ($totalAmount <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid order amount"]);
        exit;
    }

    try {
        mysqli_begin_transaction($connection);

        /* Create order */
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO `order` (User_Id, Total_Amount, Payment_Method, Status)
             VALUES (?, ?, ?, 'PENDING')"
        );
        mysqli_stmt_bind_param($stmt, "ids", $userId, $totalAmount, $paymentMethod);
        mysqli_stmt_execute($stmt);
        $orderId = mysqli_insert_id($connection);

        /* Create payment (INITIATED) */
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO payment_details
             (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status)
             VALUES (?, CURDATE(), ?, ?, 'INITIATED')"
        );
        mysqli_stmt_bind_param($stmt, "isd", $orderId, $paymentMethod, $totalAmount);
        mysqli_stmt_execute($stmt);

        /* Insert order item */
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO order_item
            (Order_Id, Product_Id, Quantity, Price_Snapshot,
             Gift_Wrapping, Personalized_Message, Is_Hamper_Suggested)
             VALUES (?, ?, 1, ?, ?, '', ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "iidii",
            $orderId,
            $productId,
            $totalAmount,
            $giftWrap > 0 ? 1 : 0,
            $hamperSelected
        );
        mysqli_stmt_execute($stmt);

        mysqli_commit($connection);

        $_SESSION['pending_order_id'] = $orderId;

        echo json_encode(["success" => true, "order_id" => $orderId]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($connection);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
        exit;
    }
}

/* =======================================================
   CART FLOW
   ======================================================= */

$totalAmount = (float) ($_SESSION['total'] ?? 0);
if ($totalAmount <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid cart total"]);
    exit;
}

/* Fetch cart */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Cart_Id FROM cart WHERE User_Id=?"
);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$cart = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$cart) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

/* Fetch cart items */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Product_Id, Quantity, Price, Gift_Wrapping, Custom_Text
     FROM customize_cart_details
     WHERE Cart_Id=?"
);
mysqli_stmt_bind_param($stmt, "i", $cart['Cart_Id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($res)) {
    if ($row['Quantity'] <= 0 || $row['Price'] <= 0) continue;

    $items[] = [
        'product_id' => (int)$row['Product_Id'],
        'qty'        => (int)$row['Quantity'],
        'price'      => (float)$row['Price'],
        'gift_wrap'  => (int)$row['Gift_Wrapping'],
        'message'    => trim($row['Custom_Text'] ?? '')
    ];
}

if (empty($items)) {
    echo json_encode(["success" => false, "message" => "No valid cart items"]);
    exit;
}

try {
    mysqli_begin_transaction($connection);

    /* Create order */
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO `order` (User_Id, Total_Amount, Status)
         VALUES (?, ?, 'PENDING')"
    );
    mysqli_stmt_bind_param($stmt, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($connection);

    /* Create payment (INITIATED) */
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO payment_details
         (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status)
         VALUES (?, CURDATE(), ?, ?, 'INITIATED')"
    );
    mysqli_stmt_bind_param($stmt, "isd", $orderId, $paymentMethod, $totalAmount);
    mysqli_stmt_execute($stmt);

    /* Insert cart items */
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
            "iiidisi",
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
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

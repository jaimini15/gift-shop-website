<?php
session_start();
include("../AdminPanel/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['User_Id'])) {
    echo json_encode(["success"=>false,"error"=>"Login required"]);
    exit;
}

$userId = $_SESSION['User_Id'];
$isBuyNow = !empty($_SESSION['buy_now']);
if (!empty($_SESSION['pending_order_id'])) {
    echo json_encode([
        "success" => false,
        "pending" => true,
        "order_id" => $_SESSION['pending_order_id']
    ]);
    exit;
}

$hamperSelected = $_SESSION['hamper_selected'] ?? 0;

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['payment_method'])) {
    echo json_encode(["success"=>false,"error"=>"Payment method required"]);
    exit;
}

/* ================= BUY NOW ================= */
if ($isBuyNow) {

    $productId = $_SESSION['buy_now_product_id'] ?? 0;
    if (!$productId) {
        echo json_encode(["success"=>false,"error"=>"Invalid product"]);
        exit;
    }

    $stmt = mysqli_prepare($connection,
        "SELECT Price FROM Product_Details WHERE Product_Id=?"
    );
    mysqli_stmt_bind_param($stmt,"i",$productId);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$product) {
        echo json_encode(["success"=>false,"error"=>"Product not found"]);
        exit;
    }
    $totalAmount = $_SESSION['buy_now_total'] ?? (float)$product['Price'];

    mysqli_begin_transaction($connection);

    try {
        $stmt = mysqli_prepare($connection,
            "INSERT INTO `order` (User_Id, Total_Amount, Status)
             VALUES (?, ?, 'PENDING')"
        );
        mysqli_stmt_bind_param($stmt,"id",$userId,$totalAmount);

        // mysqli_stmt_bind_param($stmt,"id",$userId,$product['Price']);
        mysqli_stmt_execute($stmt);

        $orderId = mysqli_insert_id($connection);

        $stmt = mysqli_prepare($connection,
            "INSERT INTO order_item
            (Order_Id, Product_Id, Quantity, Price_Snapshot, Is_Hamper_Suggested)
            VALUES (?, ?, 1, ?, ?)"
        );
        mysqli_stmt_bind_param(
    $stmt,"iidi",
    $orderId,$productId,$totalAmount,$hamperSelected
);

        mysqli_stmt_execute($stmt);

        mysqli_commit($connection);
        $_SESSION['pending_order_id'] = $orderId;

        echo json_encode(["success"=>true,"order_id"=>$orderId]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($connection);
        echo json_encode(["success"=>false,"error"=>"Buy now failed"]);
        exit;
    }
}

/* ================= CART ================= */

$stmt = mysqli_prepare($connection,
    "SELECT Cart_Id FROM cart WHERE User_Id=?"
);
mysqli_stmt_bind_param($stmt,"i",$userId);
mysqli_stmt_execute($stmt);
$cart = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$cart) {
    echo json_encode(["success"=>false,"error"=>"Cart empty"]);
    exit;
}

$stmt = mysqli_prepare($connection,
    "SELECT SUM(Quantity*Price) total
     FROM customize_cart_details WHERE Cart_Id=?"
);
mysqli_stmt_bind_param($stmt,"i",$cart['Cart_Id']);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$totalAmount = $_SESSION['total'] ?? 0;
if ($totalAmount <= 0) {
    echo json_encode(["success"=>false,"error"=>"Invalid total"]);
    exit;
}


if (($row['total'] ?? 0) <= 0) {
    echo json_encode(["success"=>false,"error"=>"Cart empty"]);
    exit;
}

mysqli_begin_transaction($connection);

try {
    $stmt = mysqli_prepare($connection,
        "INSERT INTO `order` (User_Id, Total_Amount, Status)
         VALUES (?, ?, 'PENDING')"
    );
    mysqli_stmt_bind_param($stmt,"id",$userId,$row['total']);
    mysqli_stmt_execute($stmt);

    $orderId = mysqli_insert_id($connection);

    $items = mysqli_prepare($connection,
        "SELECT Product_Id, Quantity, Price, Custom_Text, Custom_Image
         FROM customize_cart_details WHERE Cart_Id=?"
    );
    mysqli_stmt_bind_param($items,"i",$cart['Cart_Id']);
    mysqli_stmt_execute($items);
    $res = mysqli_stmt_get_result($items);

    $ins = mysqli_prepare($connection,
        "INSERT INTO order_item
        (Order_Id, Product_Id, Quantity, Price_Snapshot, Custom_Text, Custom_Image, Is_Hamper_Suggested)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    while ($i = mysqli_fetch_assoc($res)) {
        mysqli_stmt_bind_param(
            $ins,"iiidssi",
            $orderId,$i['Product_Id'],$i['Quantity'],$i['Price'],
            $i['Custom_Text'],$i['Custom_Image'],$hamperSelected
        );
        mysqli_stmt_execute($ins);
    }

    mysqli_commit($connection);
    $_SESSION['pending_order_id'] = $orderId;

    echo json_encode(["success"=>true,"order_id"=>$orderId]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(["success"=>false,"error"=>"Order failed"]);
}

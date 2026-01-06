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
        mysqli_stmt_bind_param($stmt,"id",$userId,$product['Price']);
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
" SELECT 
    ccd.Product_Id,
    ccd.Quantity,
    ccd.Price,
    IF(ccd.Custom_Text IS NULL OR ccd.Custom_Text = '', 
       pd.Product_Default_Text, 
       ccd.Custom_Text
    ) AS Final_Custom_Text,
    ccd.Custom_Image,
    ccd.Gift_Wrapping,
    ccd.Personalized_Message
FROM customize_cart_details ccd
JOIN product_details pd ON pd.Product_Id = ccd.Product_Id
WHERE ccd.Cart_Id = ?
"
);
mysqli_stmt_bind_param($items,"i",$cart['Cart_Id']);
mysqli_stmt_execute($items);
$res = mysqli_stmt_get_result($items);


    $ins = mysqli_prepare($connection,
"INSERT INTO order_item
(
 Order_Id,
 Product_Id,
 Quantity,
 Price_Snapshot,
 Custom_Text,
 Custom_Image,
 Gift_Wrapping,
 Personalized_Message,
 Is_Hamper_Suggested
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);


    while ($i = mysqli_fetch_assoc($res)) {
       mysqli_stmt_bind_param(
    $ins,
    "iiidssisi",
    $orderId,
    $i['Product_Id'],
    $i['Quantity'],
    $i['Price'],
    $i['Final_Custom_Text'],
    $i['Custom_Image'],
    $i['Gift_Wrapping'],
    $i['Personalized_Message'],
    $hamperSelected
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

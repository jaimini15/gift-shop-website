<?php
session_start();
include("../AdminPanel/db.php");

/* -------------------------
   BASIC VALIDATION
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['product_id'])) {
    header("Location: ../home page/index.php");
    exit;
}

$productId = (int) $_POST['product_id'];

/* -------------------------
   FETCH PRODUCT (READ ONLY)
-------------------------- */
$stmt = mysqli_prepare(
    $connection,
    "SELECT Product_Id, Product_Name, Price
     FROM Product_Details
     WHERE Product_Id = ? AND Status = 'Enabled'
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    header("Location: ../home page/index.php");
    exit;
}

/* -------------------------
   CLEAR OLD BUY NOW DATA
-------------------------- */
unset(
    $_SESSION['buy_now'],
    $_SESSION['buy_now_item'],
    $_SESSION['buy_now_product_id'],
    $_SESSION['gift_wrap'],
    $_SESSION['gift_card']
);

/* -------------------------
   STORE BUY NOW DATA
-------------------------- */
$_SESSION['buy_now'] = true;

/* Unified Buy Now Item */
$_SESSION['buy_now_item'] = [
    'product_id'   => (int) $product['Product_Id'],
    'product_name' => $product['Product_Name'],
    'price'        => (float) $product['Price'],
    'gift_wrap'    => !empty($_POST['gift_wrap']) ? 1 : 0,
    'gift_card'    => !empty($_POST['gift_card']) ? 1 : 0,
    'custom_text'  => trim($_POST['custom_text'] ?? ''),
    'gift_msg'     => trim($_POST['gift_card_msg'] ?? '')
];

/* Compatibility session keys (used in payment.php) */
$_SESSION['buy_now_product_id'] = (int) $product['Product_Id'];
$_SESSION['gift_wrap'] = $_SESSION['buy_now_item']['gift_wrap'];
$_SESSION['gift_card'] = $_SESSION['buy_now_item']['gift_card'];

/* -------------------------
   REDIRECT TO CART / CHECKOUT
-------------------------- */
header("Location: view_cart.php?buy_now=1");
exit;

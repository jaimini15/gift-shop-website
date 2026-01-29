<?php
session_start();
// DB CONNECTION
include '../AdminPanel/db.php'; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}
// CHECK USER LOGIN
if (!isset($_SESSION['User_Id'])) {
    die("User not logged in");
}
$userId    = (int) $_SESSION['User_Id'];
$productId = (int) ($_POST['product_id'] ?? 0);
$quantity  = 1;

if ($productId <= 0) {
    die("Invalid product");
}
// gift wrap and gift card
$giftWrap = (isset($_POST['gift_wrap']) && $_POST['gift_wrap'] == "1") ? 1 : 0;
$giftCard = (isset($_POST['gift_card']) && $_POST['gift_card'] == "1") ? 1 : 0;

$giftCardMsg = (!empty($_POST['gift_card_msg']))
    ? trim($_POST['gift_card_msg'])
    : null;

$customText = (!empty($_POST['custom_text']))
    ? trim($_POST['custom_text'])
    : null;

// PRICE CALCULATION
$wrapPrice = 39;
$cardPrice = 50;

$stmt = mysqli_prepare(
    $connection,
    "SELECT Price FROM product_details WHERE Product_Id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $productPrice);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$productPrice) {
    die("Invalid product");
}

$productPrice = (float) $productPrice;

// Calculate final price
$totalPrice = $productPrice;
if ($giftWrap) $totalPrice += $wrapPrice;
if ($giftCard) $totalPrice += $cardPrice;

// IMAGE UPLOAD
$uploadPath = null;

if (!empty($_FILES['custom_image']['name'])) {

    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $newName = time() . "_" . basename($_FILES['custom_image']['name']);
    $targetFile = $targetDir . $newName;

    if (move_uploaded_file($_FILES['custom_image']['tmp_name'], $targetFile)) {
        $uploadPath = "uploads/" . $newName;
    }
}

// CREATE CART
$cartId = null;

$stmt = mysqli_prepare(
    $connection,
    "SELECT Cart_Id FROM cart WHERE User_Id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $cartId);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$cartId) {
    $stmt = mysqli_prepare(
        $connection,
        "INSERT INTO cart (User_Id) VALUES (?)"
    );
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $cartId = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);
}
$_SESSION['cart_id'] = $cartId;

// INSERT INTO customize_cart_details
$stmt = mysqli_prepare(
    $connection,
    "INSERT INTO customize_cart_details
    (Cart_Id, Product_Id, Quantity, Price, Custom_Image, Gift_Wrapping, Custom_Text, Personalized_Message)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "iiidssss",   
    $cartId,
    $productId,
    $quantity,
    $totalPrice,
    $uploadPath,
    $giftWrap,
    $customText,
    $giftCardMsg
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("Location: product_display.php?product_id=$productId&success=1");
exit;

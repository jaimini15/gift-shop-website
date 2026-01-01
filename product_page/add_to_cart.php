<?php
session_start();
include '../AdminPanel/db.php'; // Creates $connection

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

//    CHECK USER LOGIN

if (!isset($_SESSION['User_Id'])) {
    die("User not logged in");
}

$userId     = (int) $_SESSION['User_Id'];
$productId  = (int) $_POST['product_id'];
$quantity   = 1;
//  EXTRA FIELDS options
$giftWrap       = (isset($_POST['gift_wrap']) && $_POST['gift_wrap'] == "1") ? 1 : 0;
$giftCard       = (isset($_POST['gift_card']) && $_POST['gift_card'] == "1") ? 1 : 0;
$giftCardMsg    = isset($_POST['gift_card_msg']) && trim($_POST['gift_card_msg']) !== "" 
                    ? trim($_POST['gift_card_msg']) 
                    : null;
$customText     = isset($_POST['custom_text']) && trim($_POST['custom_text']) !== "" 
                    ? trim($_POST['custom_text']) 
                    : null;
// Fix Price of wrapping and card
$wrapPrice = 39;
$cardPrice = 50;

$query = "SELECT Price FROM product_details WHERE Product_Id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $productPrice);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$productPrice) {
    die("Invalid product");
}

$productPrice = (float)$productPrice;

//    CALCULATE TOTAL PRICE
$totalPrice = $productPrice;
if ($giftWrap == 1) $totalPrice += $wrapPrice;
if ($giftCard == 1) $totalPrice += $cardPrice;

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
$cartId = null;

$query = "SELECT Cart_Id FROM cart WHERE User_Id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $cartId);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$cartId) {
    $insertCart = "INSERT INTO cart (User_Id) VALUES (?)";
    $stmt = mysqli_prepare($connection, $insertCart);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $cartId = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);
}

$query = "
    INSERT INTO customize_cart_details
    (Cart_Id, Product_Id, Quantity, Price, Custom_Image, Gift_Wrapping, Custom_Text, Personalized_Message)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($connection, $query);

mysqli_stmt_bind_param(
    $stmt,
    "iiidsiss",
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

?>

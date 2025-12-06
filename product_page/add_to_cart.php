<?php
session_start();
include '../AdminPanel/db.php'; // creates $connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['User_Id'])) {
        die("User not logged in");
    }

    $userId = $_SESSION['User_Id'];
    $productId = $_POST['product_id'];
    $quantity = 1;

    /* -------------------------
       EXTRA FIELDS
       ------------------------- */
    $giftWrap     = isset($_POST['gift_wrap']) ? 1 : 0;
    $giftCard     = isset($_POST['gift_card']) ? 1 : 0;
    $giftCardMsg  = !empty($_POST['gift_card_msg']) ? $_POST['gift_card_msg'] : null;
    $customText   = !empty($_POST['custom_text']) ? $_POST['custom_text'] : null;

    /* PRICES */
    $wrapPrice = 39;
    $cardPrice = 50;

    /* -------------------------
       GET PRODUCT PRICE
       ------------------------- */
    $result = mysqli_query($connection, "SELECT Price FROM product_details WHERE Product_Id = $productId");
    $row = mysqli_fetch_assoc($result);
    $productPrice = (float)$row['Price'];

    /* -------------------------
       CALCULATE TOTAL PRICE
       ------------------------- */
    $totalPrice = $productPrice;

    if ($giftWrap == 1)  $totalPrice += $wrapPrice;
    if ($giftCard == 1)  $totalPrice += $cardPrice;

    /* -------------------------
       IMAGE UPLOAD
       ------------------------- */
    $uploadPath = null;

    if (!empty($_FILES['custom_image']['name'])) {

        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["custom_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["custom_image"]["tmp_name"], $targetFile)) {
            $uploadPath = "uploads/" . $fileName;
        }
    }

    /* -------------------------
       GET OR CREATE CART
       ------------------------- */
    $cartQuery = "SELECT Cart_Id FROM cart WHERE User_Id = ?";
    $cartStmt = mysqli_prepare($connection, $cartQuery);
    mysqli_stmt_bind_param($cartStmt, "i", $userId);
    mysqli_stmt_execute($cartStmt);
    mysqli_stmt_bind_result($cartStmt, $cartId);
    mysqli_stmt_fetch($cartStmt);
    mysqli_stmt_close($cartStmt);

    if (!$cartId) {
        $insertCart = "INSERT INTO cart (User_Id) VALUES (?)";
        $stmt = mysqli_prepare($connection, $insertCart);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $cartId = mysqli_insert_id($connection);
        mysqli_stmt_close($stmt);
    }

    /* -------------------------
       INSERT FINAL VALUES
       ------------------------- */
    $sql = "INSERT INTO customize_cart_details 
            (Cart_Id, Product_Id, Quantity, Price, Custom_Image, Gift_Wrapping, Custom_Text, Personalized_Message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "iiidssss", 
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
}
?>

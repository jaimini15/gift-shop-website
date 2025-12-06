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
       GET EXTRA CUSTOM FIELDS
       ------------------------- */
    $giftWrap     = isset($_POST['gift_wrap']) ? (int)$_POST['gift_wrap'] : 0;
    $giftCard     = isset($_POST['gift_card']) ? (int)$_POST['gift_card'] : 0;
    $giftCardMsg  = !empty($_POST['gift_card_msg']) ? $_POST['gift_card_msg'] : null;
    $customText   = !empty($_POST['custom_text']) ? $_POST['custom_text'] : null;

    $uploadPath = null;

    /* -------------------------
       IMAGE UPLOAD (ONLY USER IMAGE)
       ------------------------- */
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
       GET OR CREATE CART FOR USER
       ------------------------- */
    $cartQuery = "SELECT Cart_Id FROM cart WHERE User_Id = ?";
    $cartStmt = mysqli_prepare($connection, $cartQuery);
    mysqli_stmt_bind_param($cartStmt, "i", $userId);
    mysqli_stmt_execute($cartStmt);
    mysqli_stmt_bind_result($cartStmt, $cartId);
    mysqli_stmt_fetch($cartStmt);
    mysqli_stmt_close($cartStmt);

    // If cart doesn't exist → create one
    if (!$cartId) {
        $insertCart = "INSERT INTO cart (User_Id) VALUES (?)";
        $stmt = mysqli_prepare($connection, $insertCart);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $cartId = mysqli_insert_id($connection);
        mysqli_stmt_close($stmt);
    }

    /* -------------------------
       INSERT INTO customize_cart_details
       ------------------------- */
    $sql = "INSERT INTO customize_cart_details 
            (Cart_Id, Product_Id, Quantity, Price, Custom_Image, Gift_Wrapping, Custom_Text, Personalized_Message)
            VALUES (
                ?, ?, ?, 
                (SELECT Price FROM product_details WHERE Product_Id = ?),
                ?, ?, ?, ?
            )";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "iiiissss", 
        $cartId, 
        $productId, 
        $quantity, 
        $productId, 
        $uploadPath,
        $giftWrap,
        $customText,
        $giftCardMsg
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    /* -------------------------
       REDIRECT BACK TO PRODUCT PAGE
       ------------------------- */
    header("Location: product_display.php?product_id=$productId&success=1");
    exit;
}
?>

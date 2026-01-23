<?php
session_start();
include("../AdminPanel/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (
    empty($_POST['order_id']) ||
    empty($_POST['razorpay_payment_id']) ||
    empty($_SESSION['User_Id'])
) {
    die("Invalid request");
}

$orderId       = (int)$_POST['order_id'];
$razorpayPayId = mysqli_real_escape_string($connection, $_POST['razorpay_payment_id']);
$userId        = (int)$_SESSION['User_Id'];
$amount        = (float)($_SESSION['total'] ?? 0);

/* ===== START TRANSACTION ===== */
mysqli_begin_transaction($connection);

try {

    /* 1️⃣ UPDATE ORDER STATUS */
    mysqli_query($connection, "
        UPDATE `order`
        SET Status = 'CONFIRM'
        WHERE Order_Id = $orderId AND User_Id = $userId
    ");

    /* 2️⃣ INSERT PAYMENT DETAILS */
    mysqli_query($connection, "
        INSERT INTO payment_details
        (Order_Id, Payment_Date, Payment_Method, Amount, Payment_Status, Transaction_Reference)
        VALUES
        ($orderId, CURDATE(), 'RAZORPAY', $amount, 'SUCCESS', '$razorpayPayId')
    ");

    /* 3️⃣ CLEAR CUSTOMIZE CART DETAILS */
    mysqli_query($connection, "
        DELETE ccd FROM customize_cart_details ccd
        JOIN cart c ON c.Cart_Id = ccd.Cart_Id
        WHERE c.User_Id = $userId
    ");

    /* 4️⃣ CLEAR CART */
    mysqli_query($connection, "
        DELETE FROM cart WHERE User_Id = $userId
    ");

    /* 5️⃣ COMMIT */
    mysqli_commit($connection);

    /* 6️⃣ REDIRECT */
    header("Location: order_summary.php?order_id=$orderId");
    exit;

} catch (Exception $e) {

    mysqli_rollback($connection);
    echo "Payment processing failed";
}

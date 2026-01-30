<?php
session_start();
include("../AdminPanel/db.php");

$currentStep = 3; 
include("checkout_steps.php");

if (!isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit;
}

$orderId = intval($_GET['order_id']);
$userId  = $_SESSION['User_Id'] ?? null;

if (!$userId) {
    header("Location: ../login/login.php");
    exit;
}

/* FETCH ORDER DETAILS */
$orderQuery = "
    SELECT 
        o.Order_Id,
        o.Order_Date,
        o.Total_Amount,
        p.Payment_Method
    FROM `order` o
    LEFT JOIN payment_details p 
        ON p.Order_Id = o.Order_Id
    WHERE o.Order_Id = '$orderId'
      AND o.User_Id = '$userId'
    LIMIT 1
";

$orderResult = mysqli_query($connection, $orderQuery);
$order = mysqli_fetch_assoc($orderResult);

if (!$order) {
    echo "Order not found";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Summary|GiftShop</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
.cart-header { background: #fff; border-bottom: 1px solid #ddd; height: 80px; }
        .header-inner { max-width: 1200px; margin: 0px 45px; padding: 15px 0px; display: grid; grid-template-columns: auto 1fr auto; align-items: center; }
        .logo { font-size: 26px; font-weight: 700; color: #7e2626d5;}
        .steps-wrapper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; min-width: 90px; }
        .circle { width: 28px; height: 28px; border-radius: 50%; border: 2px solid #cfcfe6; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #cfcfe6; background: #fff; }
        .label { margin-top: 6px; font-size: 14px; color: #cfcfe6; }
        .step.active .circle { border-color: #7e2626d5; color: #7e2626d5; }
        .step.active .label { color: #000; font-weight: 600; }
        .step.completed .circle { background: #7e2626d5; border-color: #7e2626d5; color: #fff; }
        .line { width: 100px; height: 2px; background: #cfcfe6; margin-bottom: 18px; }
        .active-line { background: #7e2626d5; }

*{
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    margin:0;
    background:#fff;
}

.container{
    max-width:1200px;
    margin:auto;
    padding:40px;
}

.summary-box{
    border:1px solid #7e2626d5;
    border-radius:8px;
    padding:35px 45px;
    background:#fff;
    max-width:900px;
    margin:auto;
}

.checkmark{
    font-size:60px;
    color:#1aa14a;
    margin-bottom:10px;
}
.summary-box h2{
    color:#1aa14a;
    margin:10px 0;
}

.summary-box p{
    font-size:15px;
    color:#555;
}

.order-details{
    display:grid;
    grid-template-columns: 200px 1fr;
    row-gap:12px;
    margin-top:25px;
    font-size:15px;
}

.order-details p{
    margin:0;
}

hr{
    margin:25px 0;
    border:none;
    border-top:1px solid #7e2626d5;
}
.shop-btn{
    display:inline-block;
    margin-top:30px;
    padding:14px 35px;
    background:#7e2626d5;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-size:16px;
    transition:0.3s;
}

.shop-btn:hover{
    background:#000;
}
.thank-note{
    font-size:15px;
    color:#444;
    margin-top:15px;
}
</style>
</head>

<body>

<div class="container">

    <div class="summary-box">
        <h2>Thank You for Your Order!</h2>

        <p>Your payment was successful and your order has been confirmed.</p>

        <p class="thank-note">
            We are carefully preparing your gift and will notify you once it is packed.
        </p>

        <hr>

       <div class="order-details">
    <p><strong>Order Number</strong></p>
    <p><?= $order['Order_Id']; ?></p>

    <p><strong>Date</strong></p>
    <p><?= date("d M Y", strtotime($order['Order_Date'])); ?></p>

    <p><strong>Payment Method</strong></p>
    <p><?= htmlspecialchars($order['Payment_Method']); ?></p>

    <p><strong>Total</strong></p>
    <p style="color:green;font-weight:600;">
        ₹<?= number_format($order['Total_Amount'], 2); ?>
    </p>
</div>
        <hr>
        <p class="thank-note">
           &#128150; Thank you for shopping with <strong style="color:#7e2626d5;">GiftShop</strong>.  
            We hope your gift brings happiness and smiles!
        </p>

        <a href="../home page/index.php" class="shop-btn">
           Shop More
        </a>

    </div>

</div>

</body>
</html>

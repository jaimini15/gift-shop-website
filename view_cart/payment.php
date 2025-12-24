<?php
session_start();
$currentStep = 2;
include("checkout_steps.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="view_cart.css">
</head>
<body>

<div class="container">

    <div class="left">
        <div class="payment-box">
            <div class="payment-title">Pay Online</div>
            <div class="online-only">✔ Online payment only (No Cash on Delivery)</div>

           <div class="methods">
    <p><strong>Pay using:</strong></p>

    <div class="payment-icons">
        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/UPI-Logo.png" alt="UPI">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa">
        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
       <img src="rupaylogo.jpg" alt="RuPay">

    </div>
</div>

        </div>
    </div>

    <div class="right">
        <h3>Price Details</h3>

        <div class="price-row">
            <span>Total Product Price</span>
            <span>₹2,447</span>
        </div>

        <div class="price-row">
            <span>Shipping Charges</span>
            <span>₹0</span>
        </div>

        <hr>

        <div class="price-row total">
            <span>Order Total</span>
            <span>₹2,447</span>
        </div>

        <button class="continue-btn">Continue</button>

        <p class="note">Clicking on "Continue" will not deduct any money</p>
    </div>

</div>

</body>
</html>

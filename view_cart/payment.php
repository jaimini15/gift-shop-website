<?php
session_start();
$currentStep = 2;
include("checkout_steps.php");

if (!isset($_SESSION['subtotal'], $_SESSION['total'])) {
    header("Location: view_cart.php");
    exit;
}

$subtotal = $_SESSION['subtotal'];
$shipping = $_SESSION['shipping'];
$total    = $_SESSION['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <!-- <link rel="stylesheet" href="view_cart.css"> -->
    <!-- INTERNAL CSS ONLY -->
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background: #fff;
        }

        /* ================= CHECKOUT HEADER ================= */
.cart-header {
    background: #fff;
    border-bottom: 1px solid #ddd;
    height: 80px;
}

.header-inner {
    max-width: 1200px;
    margin: auto;
    padding: 15px 40px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
}

/* LOGO */
.logo {
    font-size: 26px;
    font-weight: 700;
    color: #7e2626d5;
    letter-spacing: 1px;
}

/* STEPS WRAPPER */
.steps-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* EACH STEP */
.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 90px;
}

/* STEP CIRCLE */
.circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid #cfcfe6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #cfcfe6;
    background: #fff;
}

/* STEP LABEL */
.label {
    margin-top: 6px;
    font-size: 14px;
    color: #cfcfe6;
}

/* ACTIVE STEP */
.step.active .circle {
    border-color: #7e2626d5;
    color: #7e2626d5;
}

.step.active .label {
    color: #000;
    font-weight: 600;
}

/* COMPLETED STEP */
.step.completed .circle {
    background: #7e2626d5;
    border-color: #7e2626d5;
    color: #fff;
}

/* CONNECTING LINE */
.line {
    width: 100px;
    height: 2px;
    background: #cfcfe6;
    margin-bottom: 18px;
}

/* ACTIVE LINE */
.active-line {
    background: #7e2626d5;
}


        /* ========================= */
        /* MAIN CONTAINER */
        /* ========================= */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 40px;
        }

        form {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        /* LEFT SIDE */
        .left {
            flex: 2;
        }
        /* PAY USING TITLE WITH ICON */
.pay-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 20px;
    margin-bottom: 10px;
}

/* ICON SAME SIZE AS TEXT */
.pay-title img {
    width: 1.6em;
    height: 1.2em;
    object-fit: contain;
}

        /* RIGHT SIDE */
        .right {
            flex: 1;
            border: 1px solid #7e2626d5;
            border-radius: 8px;
            padding: 25px;
            height: fit-content;
        }

        /* ========================= */
        /* PAYMENT BOX */
        /* ========================= */
        .payment-box {
            border: 1px solid #7e2626d5;
            border-radius: 8px;
            padding: 25px;
        }

        .payment-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .online-only {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .methods p {
            margin-bottom: 10px;
            font-size:20px;
        }

        /* PAYMENT OPTION */
        .payment-group {
            margin-bottom: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .payment-option:hover {
            border-color: #7e2626d5;
            background: #faf5f5;
        }

        .payment-option input[type="radio"] {
            transform: scale(1.1);
            accent-color: #7e2626d5;
        }

        .payment-option img {
            height: 35px;
        }

        /* DESCRIPTION */
        .payment-desc {
            display: none;
            margin: 8px 0 0 40px;
            background: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
            color: #444;
        }

        .payment-group:has(input:checked) .payment-desc {
            display: block;
        }

        .payment-group:has(input:checked) .payment-option {
            border-color: #7e2626d5;
            background: #fff7f7;
        }

        /* ========================= */
        /* PRICE DETAILS */
        /* ========================= */
        .price-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
        }

        .total {
            font-size: 18px;
            font-weight: 600;
        }

        /* BUTTON */
        .continue-btn {
            width: 100%;
            background: #7e2626d5;
            color: #fff;
            padding: 14px;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        .continue-btn:hover {
            background: #000;
        }

        .note {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
            color: #666;
        }

        /* ========================= */
        /* RESPONSIVE */
        /* ========================= */
        @media (max-width: 768px) {
            form {
                flex-direction: column;
            }

            .left, .right {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

<form action="place_order.php" method="POST" id="paymentForm">

    <!-- LEFT -->
    <div class="left">
        <div class="payment-box">
            <div class="payment-title">Pay Online</div>
            <div class="online-only">✔ Online payment only (No Cash on Delivery)</div>

            <div class="methods">
                <p class="pay-title">
    <img src="razorpaylogo.png" alt="icon">
    <strong>Pay using Razorpay:</strong>
</p>


                <div class="payment-group">
                    <div class="payment-option">
                        <input type="radio" name="payment_method" id="phonepe" value="PHONEPE">
                        <label for="phonepe">
                            <img src="upilogo.png" alt="PhonePe">
                        </label>
                    </div>
                    <div class="payment-desc">
                        All UPI apps, Debit and Credit Cards | Powered by PhonePe
                    </div>
                </div>

                <div class="payment-group">
                    <div class="payment-option">
                        <input type="radio" name="payment_method" id="razorpay" value="RAZORPAY">
                        <label for="razorpay">
                            <img src="cardlogo.png" alt="Razorpay">
                        </label>
                    </div>
                    <div class="payment-desc">
                        Secure Payment done by Razorpay
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <h3>Price Details</h3>

        <div class="price-row">
            <span>Total Product Price</span>
            <span>₹<?= number_format($subtotal) ?></span>
        </div>

        <div class="price-row">
            <span>Shipping Charges</span>
            <span>₹<?= number_format($shipping) ?></span>
        </div>

        <hr>

        <div class="price-row total">
            <span>Order Total</span>
            <span>₹<?= number_format($total) ?></span>
        </div>

        <button type="submit" class="continue-btn">Place Order</button>

        <p id="paymentError" style="color:red; display:none; margin-top:10px;"></p>

        <p class="note">Clicking on "Place Order" will not deduct any money</p>
    </div>

</form>

</div>

<script>
document.getElementById("paymentForm").addEventListener("submit", function(e) {
    const selected = document.querySelector('input[name="payment_method"]:checked');
    const errorMsg = document.getElementById("paymentError");

    if (!selected) {
        e.preventDefault();
        errorMsg.style.display = "block";
        errorMsg.innerText = "⚠ Please select a payment option before placing the order";
    } else {
        errorMsg.style.display = "none";
    }
});
</script>

</body>
</html>

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

        /* ===== Overlay & Blur ===== */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999;
}

/* Blur background */
.blur {
    filter: blur(5px);
    pointer-events: none;
}

/* Popup Box */
.popup {
    background: #fff;
    width: 420px;
    border-radius: 10px;
    /* overflow: hidden; */
    animation: zoomIn 0.3s ease;
    /* height:400px; */
}

@keyframes zoomIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.popup-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popup-header h3 {
    margin: 0;
    font-size: 18px;
}

.close-btn {
    font-size: 22px;
    cursor: pointer;
}

.popup-body {
    padding: 20px;
}

.popup-body label {
    font-size: 13px;
    margin-top: 10px;
    display: block;
}

.popup-body input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.row {
    display: flex;
    gap: 10px;
}

.row div {
    flex: 1;
}

.pay-btn {
    width: 100%;
    margin-top: 15px;
    padding: 12px;
    background: #7e2626d5;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 15px;
    cursor: pointer;
}
/* ===== FULL PAYMENT MODAL ===== */
.payment-modal {
    width: 900px;
    height: 520px;  /* was 480px */
    background: #fff;
    border-radius: 12px;
    display: flex;
    overflow: hidden;
}


/* LEFT */
.modal-left {
    width: 30%;
    padding: 20px;
    background: #fafafa;
    border-right: 1px solid #eee;
}

.brand {
    font-weight: 600;
    margin-bottom: 20px;
}

.total-box {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    font-size: 18px;
}

.powered {
    font-size: 12px;
    margin-top: 30px;
    color: #888;
}

/* MIDDLE */
.modal-middle {
    width: 40%;
    padding: 20px;
    border-right: 1px solid #eee;
}

.method {
    padding: 14px;
    margin-top: 10px;
    border-radius: 8px;
    cursor: pointer;
}

.method.active {
    background: #efe7ff;
    border-left: 4px solid #7e2626d5;
}

/* RIGHT */
/* RIGHT PANEL */
.modal-right {
    width: 55%;
    padding: 30px 40px;
    overflow-y: auto;      /* ✅ enable vertical scroll */
    max-height: 100%;      /* keep inside modal */
}


.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-right input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.modal-right .row {
    display: flex;
    gap: 5px;
}

.modal-right .row input {
    flex: 1;
}

.pay-btn {
    margin-top: 15px;
    width: 100%;
    padding: 12px;
    background: #7e2626d5;
    color: #fff;
    border: none;
    border-radius: 6px;
}
/* ===== TOTAL DROPDOWN ===== */
.total-panel {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #eee;
}

.total-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.total-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

#arrow {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.total-breakup {
    display: none;
    margin-top: 12px;
    border-top: 1px solid #eee;
    padding-top: 12px;
}

.total-breakup .row {
    display: flex;
    justify-content: space-between;
    margin: 6px 0;
    font-size: 14px;
    color: #555;
}

.total-breakup .bold {
    font-weight: 600;
    color: #000;
}
/* RIGHT SECTION (MIDDLE + RIGHT) */
.modal-right-section {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* HEADER ONLY FOR MIDDLE + RIGHT */
.payment-options-header {
    height: 52px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

/* CENTER TITLE */
.payment-options-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

/* CLOSE BUTTON */
.payment-options-header .close-btn {
    position: absolute;
    right: 18px;
    font-size: 22px;
    cursor: pointer;
}

/* MIDDLE + RIGHT CONTENT */
.modal-inner {
    flex: 1;
    display: flex;
}
.card-error {
    min-height: 20px;          /* space always reserved */
    margin-bottom: 12px;
    font-size: 14px;
    font-weight: 500;
    color: #d93025;
    visibility: hidden;        /* invisible, but space kept */
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


<!-- ===== Razorpay Card Popup ===== -->

 <div class="overlay" id="paymentOverlay">
  <div class="payment-modal">

    <!-- LEFT PANEL -->
    <div class="modal-left">
      <div class="brand">GiftShop Private Ltd</div>

      <div class="total-panel">
        <div class="total-header" onclick="toggleTotal()">
          <span>Total</span>
          <div class="total-right">
            <strong>₹<?= number_format($total, 2) ?></strong>
            <span id="arrow">▼</span>
          </div>
        </div>

        <div class="total-breakup" id="totalBreakup">
          <div class="row">
            <span>Subtotal</span>
            <span>₹<?= number_format($subtotal, 2) ?></span>
          </div>
          <div class="row">
            <span>Convenience Fee</span>
            <span>₹0.00</span>
          </div>
          <div class="row bold">
            <span>Grand Total</span>
            <span>₹<?= number_format($total, 2) ?></span>
          </div>
        </div>
      </div>

      <div class="powered">Powered by PhonePe</div>
    </div>

    <!-- RIGHT SECTION (MIDDLE + RIGHT) -->
    <div class="modal-right-section">

      <!-- HEADER CENTERED BETWEEN MIDDLE & RIGHT -->
      <div class="payment-options-header">
        <h3>Payment Options</h3>
        <span class="close-btn" onclick="closePopup()">×</span>
      </div>

      <!-- CONTENT -->
      <div class="modal-inner">

        <!-- MIDDLE PANEL -->
        <div class="modal-middle">
          <div class="method active">
            💳 Debit / Credit Card
          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="modal-right">
          <h4>Debit / Credit Card</h4>

          <label>Card Number</label>
          <input type="text"  id="cardNumber" placeholder="0000 0000 0000 0000">

          <label>Name on Card</label>
          <input type="text" id="cardName" placeholder="Enter name on card">

          <div class="row">
    <div>
        <label>Expiry Date</label>
        <input type="text" id="expiry" placeholder="MM/YY" maxlength="5">
    </div>
    <div>
        <label>CVV</label>
        <input type="password" id="cvv" placeholder="CVV" maxlength="3">
    </div>
</div>
 <button class="pay-btn">Proceed</button>
<p id="cardError" class="card-error"></p>
        </div>

      </div>
    </div>

  </div>
</div>


<script>
const form = document.getElementById("paymentForm");
const overlay = document.getElementById("paymentOverlay");
const container = document.querySelector(".container");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    const selected = document.querySelector('input[name="payment_method"]:checked');
    const errorMsg = document.getElementById("paymentError");

    if (!selected) {
        errorMsg.style.display = "block";
        errorMsg.innerText = "⚠ Please select a payment option";
        return;
    }

    errorMsg.style.display = "none";

    // If CARD selected → open popup
    if (selected.value === "RAZORPAY") {
        overlay.style.display = "flex";
        container.classList.add("blur");
    } 
    // If UPI → submit normally
    else {
        form.submit();
    }
});

function closePopup() {
    overlay.style.display = "none";
    container.classList.remove("blur");
}
</script>
<script>
function toggleTotal() {
    const breakup = document.getElementById("totalBreakup");
    const arrow = document.getElementById("arrow");

    if (breakup.style.display === "block") {
        breakup.style.display = "none";
        arrow.innerText = "▼";
        arrow.style.transform = "rotate(0deg)";
    } else {
        breakup.style.display = "block";
        arrow.innerText = "▲";
        arrow.style.transform = "rotate(180deg)";
    }
}
</script>
<script>
document.querySelector(".pay-btn").addEventListener("click", function (e) {
    e.preventDefault();

    const cardNumber = document.getElementById("cardNumber").value.replace(/\s+/g, "");
    const cardName   = document.getElementById("cardName").value.trim();
    const expiry     = document.getElementById("expiry").value.trim();
    const cvv        = document.getElementById("cvv").value.trim();
    const errorBox   = document.getElementById("cardError");

    errorBox.innerText = "";

    /* ===== CARD NUMBER ===== */
    if (!/^\d{16}$/.test(cardNumber)) {
        return showError("Card number must be 16 digits");
    }

    /* ===== NAME ON CARD (exactly 2 spaces) ===== */
    if (!/^[A-Za-z]+ [A-Za-z]+ [A-Za-z]+$/.test(cardName)) {
        return showError("Name must contain only letters with exactly 2 spaces (e.g. Gargi Rahul Jain)");
    }

    /* ===== CVV ===== */
    if (!/^\d{3}$/.test(cvv)) {
        return showError("CVV must be exactly 3 digits");
    }

    /* ===== EXPIRY FORMAT ===== */
if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
    return showError("Expiry must be in MM/YY format (01–12 / future year)");
}

/* ===== EXPIRY DATE LOGIC ===== */
const [mm, yy] = expiry.split("/");
const expMonth = parseInt(mm, 10);
const expYear  = 2000 + parseInt(yy, 10); // assume 20YY only

const today = new Date();
const currentMonth = today.getMonth() + 1;
const currentYear  = today.getFullYear();

/* Reject very old cards explicitly */
if (expYear < currentYear) {
    return showError("Card expiry year must be in the future");
}

/* Reject same year but expired month */
if (expYear === currentYear && expMonth <= currentMonth) {
    return showError("Card expiry month must be in the future");
}
    /* ===== SUCCESS ===== */
   errorBox.style.visibility = "hidden";
errorBox.innerText = "";
errorBox.style.color = "#d93025";
    // 👉 Here you can trigger Razorpay
});
function showError(message) {
    const errorBox = document.getElementById("cardError");
    errorBox.innerText = message;
    errorBox.style.color = "#d93025";
    errorBox.style.visibility = "visible";
}

</script>

</body>
</html>

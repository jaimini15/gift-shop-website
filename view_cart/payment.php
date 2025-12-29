<?php
session_start();
include("../AdminPanel/db.php");
$currentStep = 2;

include("checkout_steps.php");
// Save hamper choice (default false)
$_SESSION['hamper_selected'] = isset($_POST['hamper']) ? 1 : 0;

// Validate totals
if (!isset($_SESSION['subtotal'], $_SESSION['total'])) {
    header("Location: view_cart.php");
    exit;
}

$subtotal = $_SESSION['subtotal'];
$shipping = $_SESSION['shipping'];
$total    = $_SESSION['total'];
$userId   = $_SESSION['User_Id'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        /* ===== FULL CSS FROM ORIGINAL DESIGN ===== */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; background: #fff; }

        /* Checkout Header */
        .cart-header { background: #fff; border-bottom: 1px solid #ddd; height: 80px; }
        .header-inner { max-width: 1200px; margin: auto; padding: 15px 40px; display: grid; grid-template-columns: auto 1fr auto; align-items: center; }
        .logo { font-size: 26px; font-weight: 700; color: #7e2626d5; letter-spacing: 1px; }
        .steps-wrapper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; min-width: 90px; }
        .circle { width: 28px; height: 28px; border-radius: 50%; border: 2px solid #cfcfe6; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #cfcfe6; background: #fff; }
        .label { margin-top: 6px; font-size: 14px; color: #cfcfe6; }
        .step.active .circle { border-color: #7e2626d5; color: #7e2626d5; }
        .step.active .label { color: #000; font-weight: 600; }
        .step.completed .circle { background: #7e2626d5; border-color: #7e2626d5; color: #fff; }
        .line { width: 100px; height: 2px; background: #cfcfe6; margin-bottom: 18px; }
        .active-line { background: #7e2626d5; }

        /* Main Container */
        .container { max-width: 1200px; margin: auto; padding: 40px; }
        form { display: flex; gap: 30px; align-items: flex-start; }
        .left { flex: 2; }
        .pay-title { display: flex; align-items: center; gap: 8px; font-size: 20px; margin-bottom: 10px; }
        .pay-title img { width: 1.6em; height: 1.2em; object-fit: contain; }
        .right { flex: 1; border: 1px solid #7e2626d5; border-radius: 8px; padding: 25px; height: fit-content; }

        /* Payment Box */
        .payment-box { border: 1px solid #7e2626d5; border-radius: 8px; padding: 25px; }
        .payment-title { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
        .online-only { color: green; font-size: 14px; margin-bottom: 15px; }
        .methods p { margin-bottom: 10px; font-size:20px; }
        .payment-group { margin-bottom: 15px; }
        .payment-option { display: flex; align-items: center; gap: 12px; border: 1px solid #ddd; border-radius: 6px; padding: 14px; cursor: pointer; transition: 0.2s; }
        .payment-option:hover { border-color: #7e2626d5; background: #faf5f5; }
        .payment-option input[type="radio"] { transform: scale(1.1); accent-color: #7e2626d5; }
        .payment-option img { height: 35px; }
        .payment-desc { display: none; margin: 8px 0 0 40px; background: #f7f7f7; padding: 10px; border-radius: 5px; font-size: 13px; color: #444; }
        .payment-group:has(input:checked) .payment-desc { display: block; }
        .payment-group:has(input:checked) .payment-option { border-color: #7e2626d5; background: #fff7f7; }

        /* Price Details */
        .price-row { display: flex; justify-content: space-between; margin: 12px 0; }
        .total { font-size: 18px; font-weight: 600; }
        .continue-btn { width: 100%; background: #7e2626d5; color: #fff; padding: 14px; border: none; font-size: 16px; border-radius: 5px; cursor: pointer; margin-top: 15px; }
        .continue-btn:hover { background: #000; }
        .note { text-align: center; font-size: 12px; margin-top: 10px; color: #666; }

        /* Overlay & Popup */
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 999; }
        .blur { filter: blur(5px); pointer-events: none; }
        .popup { background: #fff; width: 420px; border-radius: 10px; animation: zoomIn 0.3s ease; }
        @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .popup-header { padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .popup-header h3 { margin: 0; font-size: 18px; }
        .close-btn { font-size: 22px; cursor: pointer; }
        .popup-body { padding: 20px; }
        .popup-body label { font-size: 13px; margin-top: 10px; display: block; }
        .popup-body input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
        .row { display: flex; gap: 10px; }
        .row div { flex: 1; }
        .pay-btn { width: 100%; margin-top: 15px; padding: 12px; background: #7e2626d5; color: #fff; border: none; border-radius: 5px; font-size: 15px; cursor: pointer; }

        /* Full Payment Modal */
        .payment-modal { width: 900px; height: 520px; background: #fff; border-radius: 12px; display: flex; overflow: hidden; }
        .modal-left { width: 30%; padding: 20px; background: #fafafa; border-right: 1px solid #eee; }
        .brand { font-weight: 600; margin-bottom: 20px; }
        .total-box { background: #fff; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; font-size: 18px; }
        .powered { font-size: 12px; margin-top: 30px; color: #888; }
        .modal-middle { width: 40%; padding: 20px; border-right: 1px solid #eee; }
        .method { padding: 14px; margin-top: 10px; border-radius: 8px; cursor: pointer; }
        .method.active { background: #efe7ff; border-left: 4px solid #7e2626d5; }
        .modal-right-section { flex: 1; display: flex; flex-direction: column; }
        .payment-options-header { height: 52px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: center; position: relative; }
        .payment-options-header h3 { margin: 0; font-size: 18px; font-weight: 600; }
        .payment-options-header .close-btn { position: absolute; right: 18px; font-size: 22px; cursor: pointer; }
        .modal-inner { flex: 1; display: flex; }
        .modal-right { width: 55%; padding: 30px 40px; overflow-y: auto; max-height: 100%; }
        .modal-right input { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; }
        .modal-right .row { display: flex; gap: 5px; }
        .modal-right .row input { flex: 1; }
        .card-error { min-height: 20px; margin-bottom: 12px; font-size: 14px; font-weight: 500; color: #d93025; visibility: hidden; }

.total-breakup {
    display: none;
    margin-top: 12px;
    border-top: 1px solid #eee;
    padding-top: 12px;
    transition: max-height 0.3s ease;
    overflow: hidden;
}

#arrow {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.total-breakup.show {
    display: block;
}
/* Loading of payment process */
/* ===== LOADING OVERLAY ===== */
#loadingOverlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

/* ===== POPUP BOX ===== */
.loading-content {
    /* background: #fff; */
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    width: 350px;
}

/* ===== SPINNER ===== */
.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #eee;
    border-top: 4px solid #7e2626d5;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* ===== SUCCESS STATE ===== */
.checkmark {
    font-size: 50px;
    color: #1aa14a;
    margin-bottom: 10px;
    background-color:white;
   width:100%;
}

#successState h3 {
    background-color:white;
    color: #1aa14a;
    margin: 10px 0;
    width:100%;
}

    </style>
</head>
<body>

<div class="container">
    <form id="paymentForm">
        <!-- LEFT SIDE -->
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
                            <label for="phonepe"><img src="upilogo.png" alt="PhonePe"></label>
                        </div>
                        <div class="payment-desc">All UPI apps, Debit and Credit Cards | Powered by PhonePe</div>
                    </div>
                    <div class="payment-group">
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="razorpay" value="RAZORPAY">
                            <label for="razorpay"><img src="cardlogo.png" alt="Razorpay"></label>
                        </div>
                        <div class="payment-desc">Secure Payment done by Razorpay</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right">
            <h3>Price Details</h3>
            <div class="price-row"><span>Total Product Price</span><span>₹<?= number_format($subtotal) ?></span></div>
            <div class="price-row"><span>Shipping Charges</span><span>₹<?= number_format($shipping) ?></span></div>
            <hr>
            <div class="price-row total"><span>Order Total</span><span>₹<?= number_format($total) ?></span></div>

            <button type="button" id="placeOrderBtn" class="continue-btn">Place Order</button>
            <p id="paymentError" style="color:red; display:none; margin-top:10px;"></p>
            <p class="note">Clicking on "Place Order" will not deduct any money</p>
        </div>

        <input type="hidden" name="payment_method" id="paymentMethodInput">
    </form>
</div>

<!-- ===== PAYMENT POPUP ===== -->
<div class="overlay" id="paymentOverlay">
    <div class="payment-modal">
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


        <div class="modal-right-section">
            <div class="payment-options-header">
                <h3>Payment Options</h3>
                <span class="close-btn" onclick="closePopup()">×</span>
            </div>

            <div class="modal-inner">
                <div class="modal-middle">
                    <div class="method active">💳 Debit / Credit Card</div>
                </div>

                <div class="modal-right">

    <!-- ===== CARD PANEL (existing UI) ===== -->
    <div id="cardPanel">
        <h4>Debit / Credit Card</h4>

        <label>Card Number</label>
        <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000">

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

    <!-- ===== UPI PANEL (NEW) ===== -->
    <div id="upiPanel" style="display:none;">
        <h4>Pay using UPI ID</h4>

        <label>UPI ID</label>
        <input type="text" id="upiId" placeholder="example@upi">

        <button class="pay-btn" id="upiPayBtn">Verify & Pay</button>

        <p id="upiError" class="card-error"></p>
    </div>

</div>

            </div>
        </div>
    </div>
</div>

<script>
// ===== JS LOGIC =====
function toggleTotal() {
    const breakup = document.getElementById("totalBreakup");
    const arrow = document.getElementById("arrow");

    if (breakup.classList.contains("show")) {
        breakup.classList.remove("show");
        arrow.style.transform = "rotate(0deg)";
        arrow.innerText = "▼";
    } else {
        breakup.classList.add("show");
        arrow.style.transform = "rotate(180deg)";
        arrow.innerText = "▲";
    }
}


function showError(msg) {
    const e = document.getElementById("cardError");
    e.innerText = msg; e.style.visibility="visible";
}

function closePopup() {

    // 🔔 Step 1: Show alert (ONLY OK button)
    alert("❌ Payment failed");

    // 🔁 Step 2: Cancel order AFTER user clicks OK
    if (!window.pendingOrderId) return;

    fetch("cancel_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id: window.pendingOrderId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log("Pending order cancelled successfully");
        } else {
            console.log("Cancel failed:", data.error || data.message);
        }
    })
    .finally(() => {
        // 🧹 Step 3: Close popup & unblur page
        document.getElementById("paymentOverlay").style.display = "none";
        document.querySelector(".container").classList.remove("blur");
        window.pendingOrderId = null;
    });
}




document.getElementById("placeOrderBtn").addEventListener("click", function(){
    const selected = document.querySelector('input[name="payment_method"]:checked');
    if(!selected){ 
        const e=document.getElementById("paymentError");
        e.style.display="block"; e.innerText="⚠ Please select a payment option";
        return;
    }
    document.getElementById("paymentMethodInput").value = selected.value;

   fetch("place_order.php", {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify({payment_method:selected.value})
})

    .then(res => res.json())
.then(data => {

    // 🔁 Pending order already exists → resume payment popup
    if (!data.success && data.pending) {
        document.getElementById("paymentOverlay").style.display = "flex";
        document.querySelector(".container").classList.add("blur");
        return;
    }

    // ❌ Real error
    if (!data.success) {
        alert(data.error || data.message || "Order creation failed");
        return;
    }

    // ✅ New order created
    window.pendingOrderId = data.order_id;
    document.getElementById("paymentOverlay").style.display = "flex";
    document.querySelector(".container").classList.add("blur");

})
.catch(() => alert("Network error"));
}); // ✅ CLOSE placeOrderBtn click listener


document.querySelector(".pay-btn").addEventListener("click", function (e) {
    e.preventDefault();

    /* ================= VALIDATION ================= */
    const cardNumber = document.getElementById("cardNumber").value.replace(/\s+/g,"");
    const cardName   = document.getElementById("cardName").value.trim();
    const expiry     = document.getElementById("expiry").value.trim();
    const cvv        = document.getElementById("cvv").value.trim();

    if(!/^\d{16}$/.test(cardNumber)) return alert("Invalid card number");
    if(!/^[A-Za-z ]+$/.test(cardName)) return alert("Invalid card holder name");
    if(!/^\d{3}$/.test(cvv)) return alert("Invalid CVV");
    if(!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) return alert("Invalid expiry");

    /* ================= UI SWITCH ================= */
    document.getElementById("paymentOverlay").style.display = "none";

    const loader = document.getElementById("loadingOverlay");
    loader.style.display = "flex";

    document.getElementById("loadingState").style.display = "block";
    document.getElementById("successState").style.display = "none";

    document.querySelector(".container").classList.add("blur");

    /* ================= PAYMENT ================= */
    setTimeout(() => {

        fetch("confirm_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                order_id: window.pendingOrderId,
                payment_method: document.getElementById("paymentMethodInput").value
            })
        })
        .then(res => res.json())
        .then(data => {

            if(!data.success){
                loader.style.display="none";
                alert(data.error || "Payment failed");
                return;
            }

            /* ✅ SUCCESS STATE */
            document.getElementById("loadingState").style.display = "none";
            document.getElementById("successState").style.display = "block";

            setTimeout(() => {
                window.location.href =
                    "order_summary.php?order_id=" + data.order_id;
            }, 1500);

        });

    }, 4000); // realistic delay
});
</script>
<script>
document.getElementById("upiPayBtn").addEventListener("click", function () {

    const upiId = document.getElementById("upiId").value.trim();
    const error = document.getElementById("upiError");

    error.style.visibility = "hidden";

    if (!/^[\w.-]+@[\w.-]+$/.test(upiId)) {
        error.innerText = "Invalid UPI ID";
        error.style.visibility = "visible";
        return;
    }

    // Close payment popup
    document.getElementById("paymentOverlay").style.display = "none";

    // Show loading overlay
    document.getElementById("loadingOverlay").style.display = "flex";
    document.getElementById("loadingState").style.display = "block";
    document.getElementById("successState").style.display = "none";

    setTimeout(() => {

        fetch("confirm_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                order_id: window.pendingOrderId,
                payment_method: "UPI"
            })
        })
        .then(res => res.json())
        .then(data => {

            if (!data.success) {
                alert("UPI payment failed");
                return;
            }

            document.getElementById("loadingState").style.display = "none";
            document.getElementById("successState").style.display = "block";

            setTimeout(() => {
                window.location.href =
                    "order_summary.php?order_id=" + data.order_id;
            }, 1500);
        });

    }, 4000);
});
</script>

<script>
const upiRadio  = document.getElementById("phonepe");
const cardRadio = document.getElementById("razorpay");

const upiPanel  = document.getElementById("upiPanel");
const cardPanel = document.getElementById("cardPanel");

/* Switch UI when payment method changes */
upiRadio.addEventListener("change", () => {
    upiPanel.style.display = "block";
    cardPanel.style.display = "none";
});

cardRadio.addEventListener("change", () => {
    upiPanel.style.display = "none";
    cardPanel.style.display = "block";
});
</script>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay">
    <div class="loading-content">

        <!-- LOADING -->
        <div id="loadingState">
            <h3>Your payment is processing</h3>
            <div class="spinner"></div>
            <p>Please do not refresh the page</p>
        </div>

        <!-- SUCCESS -->
        <div id="successState" style="display:none;">
            <div class="checkmark">✔</div>
            <h3>Order Confirmed</h3>
            <p>Your payment was successful</p>
        </div>

    </div>
</div>


</body>
</html>

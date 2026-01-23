<?php
session_start();
$currentStep = 1;
include("checkout_steps.php");

include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "<p class='empty-msg'>Please login to view cart.</p>";
    exit;
}

$uid = $_SESSION['User_Id'];

/* Fetch Cart */
$cartRes = mysqli_query($connection, "SELECT Cart_Id FROM cart WHERE User_Id='$uid'");
$cart = mysqli_fetch_assoc($cartRes);

if (!$cart) {
    echo "<p class='empty-msg'>Your cart is empty.</p>";
    exit;
}

$cartId = $cart['Cart_Id'];

/* Fetch Cart Items */
$query = "
    SELECT ccd.*, pd.Product_Name, pd.Product_Image
    FROM customize_cart_details ccd
    JOIN product_details pd ON ccd.Product_Id = pd.Product_Id
    WHERE ccd.Cart_Id = '$cartId'
";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) == 0) {
   header("location: ../home page/index.php");
    exit;
}
$outOfStockProducts = [];

$stockCheckQuery = "
    SELECT 
        ccd.Product_Id,
        pd.Product_Name,
        sd.Stock_Available,
        ccd.Quantity
    FROM customize_cart_details ccd
    JOIN product_details pd ON pd.Product_Id = ccd.Product_Id
    LEFT JOIN stock_details sd ON sd.Product_Id = ccd.Product_Id
    WHERE ccd.Cart_Id = '$cartId'
";

$stockResult = mysqli_query($connection, $stockCheckQuery);

while ($row = mysqli_fetch_assoc($stockResult)) {
    $available = (int)($row['Stock_Available'] ?? 0);

    if ($available < $row['Quantity']) {
        $outOfStockProducts[] = $row['Product_Name'];
    }
}

$subtotal = 0;


/* Estimated Delivery Date */
$estimatedDate = date("d M Y", strtotime("+3 days"));
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart</title>
<link rel="stylesheet" href="view_cart.css">
<style>
.stock-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.stock-modal {
    background: #fff;
    padding: 25px;
    width: 360px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.stock-modal h2 {
    color: #d32f2f;
    margin-bottom: 10px;
}

.stock-modal ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.stock-modal li {
    padding: 6px 0;
    font-weight: bold;
}



.stock-modal button {
    margin-top: 15px;
    padding: 10px 25px;
    border: none;
    background: #7e2626d5;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
}
.stock-modal button:hover {
    background:black;
}
</style>
</head>
<?php if (!empty($outOfStockProducts)): ?>
<div class="stock-modal-overlay">
    <div class="stock-modal">
        <h2>&#9888;Items Out of Stock</h2>
        <p>Please remove the following product(s) to continue:</p>

        <ul>
            <?php foreach ($outOfStockProducts as $p): ?>
                <li><?= htmlspecialchars($p) ?></li>
            <?php endforeach; ?>
        </ul>

        <button onclick="closeStockModal()">OK</button>
    </div>
</div>
<?php endif; ?>

<body>
<!-- MAIN -->
<div class="cart-container">

<!-- LEFT -->
<div class="cart-left">
<h2>Product Details</h2>
<?php 
$totalItems = 0;
while ($row = mysqli_fetch_assoc($result)) :
    $img = "data:image/jpeg;base64," . base64_encode($row['Product_Image']);
    $price = $row['Price'];
    $qty   = $row['Quantity'];
    $subtotal += ($price * $qty);
     $totalItems += $qty;
?>
<br/>
<div class="cart-box">
    <div class="cart-item">
        <img src="<?= $img ?>" alt="product">
        <div class="item-details">
            <h3><?= htmlspecialchars($row['Product_Name']) ?></h3>
            <p class="price">₹<?= number_format($price) ?></p>
            <p>Qty: <?= $qty ?></p>
            <p class="return">No return No refund</p>
            <a href="#" class="remove" data-id="<?= $row['Customize_Id'] ?>">✕ REMOVE</a>
        </div>
    </div>

    <div class="delivery-row">
        <span><strong>Estimated delivery by</strong></span>
        <span class="date"><?= $estimatedDate ?></span>
    </div>
</div>


<?php endwhile; ?>
</div>

<!-- RIGHT -->
<?php
$shipping = 0; // example
$total = max(0, $subtotal - $shipping);

/* STORE IN SESSION */
$_SESSION['subtotal'] = $subtotal;
$_SESSION['shipping'] = $shipping;
$_SESSION['total']    = $total;
?>
<div class="cart-right">
<h3>Price Details</h3>

<div class="price-row">
    <span>Total Product Price</span>
    <span>₹<?= number_format($subtotal) ?></span>
</div>

<div class="price-row shipping">
    <span>Shipping Charges</span>
    <span>-₹<?= $shipping ?></span>
</div>

<hr>

<div class="price-row total">
    <span>Order Total</span>
    <span>₹<?= number_format($total) ?></span>
</div>
<?php if (!empty($outOfStockProducts)): ?>


<style>
.continue-btn {
    pointer-events: none;
    opacity: 0.5;
}
</style>
<?php endif; ?>

<button type="button" id="continueBtn" class="continue-btn">
    Continue
</button>
<p class="note">Clicking on "Continue" will not deduct any money</p>
</div>

</div>
<script>
document.addEventListener("click", function(e) {

    if (e.target.classList.contains("remove")) {

        e.preventDefault();
        let id = e.target.dataset.id;

        fetch("../cart/remove_cart_item.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "customize_id=" + id
        })
        .then(res => res.text())
        .then(res => {
            if (res.trim() === "success") {
                e.target.closest(".cart-item").remove();
                location.reload();
            }
        });
    }

});
</script>
<script>
function closeStockModal() {
    document.querySelector('.stock-modal-overlay').style.display = 'none';
}
</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById("continueBtn").addEventListener("click", function () {

    // STEP 1: create pending order in DB
    fetch("place_order.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ payment_method: "RAZORPAY" })
    })
    .then(res => res.json())
    .then(order => {

        if (!order.success && !order.pending) {
            alert(order.error || "Unable to create order");
            return;
        }

        window.pendingOrderId = order.order_id;

        // STEP 2: create Razorpay order
        fetch("create_razorpay_order.php")
        .then(res => res.json())
        .then(rzp => {

            if (!rzp.success) {
                alert("Unable to start payment");
                return;
            }

            // STEP 3: open Razorpay popup
            var options = {
                key: rzp.key,
                amount: rzp.amount,
                currency: "INR",
                name: "GiftShop Pvt Ltd",
                description: "Order Payment",
                order_id: rzp.orderId,

               handler: function (response) {

    fetch("confirm_payment.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            razorpay_payment_id: response.razorpay_payment_id,
            order_id: window.pendingOrderId
        })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            window.location.href = "order_summary.php?order_id=" + result.order_id;
        } else {
            alert("Payment failed");
        }
    });
},

                modal: {
                    ondismiss: function () {
                        // cancel pending order if user closes popup
                        fetch("cancel_order.php", {
                            method: "POST",
                            headers: {"Content-Type": "application/json"},
                            body: JSON.stringify({ order_id: window.pendingOrderId })
                        });
                    }
                },

                theme: { color: "#7e2626" }
            };

            new Razorpay(options).open();
        });
    });
});
</script>

</body>
</html>

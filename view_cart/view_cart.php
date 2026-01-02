<?php
session_start();

$isBuyNow = isset($_GET['buy_now']) && $_GET['buy_now'] == 1
            && isset($_SESSION['buy_now'], $_SESSION['buy_now_item']);

$currentStep = 1;
include("checkout_steps.php");

include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "<p class='empty-msg'>Please login to view cart.</p>";
    exit;
}

$uid = $_SESSION['User_Id'];

  $items = [];
$subtotal = 0;
$totalItems = 0;

/* ================= BUY NOW FLOW ================= */
if ($isBuyNow) {

    $item = $_SESSION['buy_now_item'];

    /* Fetch product image */
    $stmt = mysqli_prepare(
        $connection,
        "SELECT Product_Image FROM product_details WHERE Product_Id=? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "i", $item['product_id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $imgRow = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    $items[] = [
        'product_id'   => $item['product_id'],
        'Product_Name' => $item['product_name'],
        'Price'        => $item['price'],
        'Quantity'     => 1,
        'Product_Image'=> $imgRow['Product_Image'],
        'buy_now'      => true
    ];

}
/* ================= NORMAL CART FLOW ================= */
else {

    $cartRes = mysqli_query($connection, "SELECT Cart_Id FROM cart WHERE User_Id='$uid'");
    $cart = mysqli_fetch_assoc($cartRes);

    if (!$cart) {
        echo "<p class='empty-msg'>Your cart is empty.</p>";
        exit;
    }

    $cartId = $cart['Cart_Id'];

    $query = "
        SELECT ccd.*, pd.Product_Name, pd.Product_Image
        FROM customize_cart_details ccd
        JOIN product_details pd ON ccd.Product_Id = pd.Product_Id
        WHERE ccd.Cart_Id = '$cartId'
    ";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "<p class='empty-msg'>Your cart is empty.</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
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
</head>
<body>
<!-- MAIN -->
<div class="cart-container">

<!-- LEFT -->
<div class="cart-left">
<h2>Product Details</h2>
<?php 
$totalItems = 0;
foreach ($items as $row) :
    $img = "data:image/jpeg;base64," . base64_encode($row['Product_Image']);
    $price = $row['Price'];
    $qty   = $row['Quantity'];
    $subtotal += ($price * $qty);
     $totalItems += $qty;
?>
<br>
<div class="cart-box">
    <div class="cart-item">
        <img src="<?= $img ?>" alt="product">
        <div class="item-details">
            <h3><?= htmlspecialchars($row['Product_Name']) ?></h3>
            <p class="price">₹<?= number_format($price) ?></p>
            <p>Qty: <?= $qty ?></p>
            <p class="return">No return No refund</p>
            <?php if (empty($row['buy_now'])): ?>
<a href="#" class="remove" data-id="<?= $row['Customize_Id'] ?>">✕ REMOVE</a>
<?php endif; ?>

        </div>
    </div>

    <div class="delivery-row">
        <span><strong>Estimated delivery by</strong></span>
        <span class="date"><?= $estimatedDate ?></span>
    </div>
</div>


<?php endforeach; ?>

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
<form action="payment.php" method="POST">
    <?php if ($totalItems >= 3): ?>
<div style="margin:15px 0;">
    <label>
        <input type="checkbox" name="hamper" value="1"
            <?= !empty($_SESSION['hamper_selected']) ? 'checked' : '' ?>>
         🎁 You have 3 or more items. Pack as gift hamper?
    </label>
</div>
<?php endif; ?>
    <button type="submit" class="continue-btn">
        Continue
    </button>
</form>


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
let continueClicked = false;

document.querySelector(".continue-btn").addEventListener("click", function () {
    continueClicked = true;
});

/* Browser back / close / refresh */
window.addEventListener("beforeunload", function () {
    if (!continueClicked) {
        navigator.sendBeacon("../cart/delete_cart.php");
    }
});
</script>

</body>
</html>

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

$subtotal = 0;
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
<?php while ($row = mysqli_fetch_assoc($result)) :

    $img = "data:image/jpeg;base64," . base64_encode($row['Product_Image']);
    $price = $row['Price'];
    $qty   = $row['Quantity'];
    $subtotal += ($price * $qty);
?>
<br>
<div class="cart-item">
    <img src="<?= $img ?>">
    <div class="item-details">
        <h3><?= htmlspecialchars($row['Product_Name']) ?></h3>
        <p class="price">₹<?= number_format($price) ?></p>
        <p>Qty: <?= $qty ?></p>
        <p class="return">No return No refund</p>
        <a href="#" class="remove" data-id="<?= $row['Customize_Id'] ?>">✕ REMOVE</a>
    </div>
</div>

<?php endwhile; ?>
</div>

<!-- RIGHT -->
<?php
$shipping = 0; // example
$total = $subtotal - $shipping;
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
<a href="payment.php" style="text-decoration:none;">
<button class="continue-btn">Continue</button>
</a>
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

</body>
</html>

<?php
session_start();
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
    echo "<p class='empty-msg'>Your cart is empty.</p>";
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

<!-- HEADER -->
<header class="cart-header">
    <div class="logo">GiftShop</div>

    <div class="steps-wrapper">
        <div class="step active">
            <span class="circle">1</span>
            <span class="label">Cart</span>
        </div>
        <div class="line"></div>
        <div class="step">
            <span class="circle">2</span>
            <span class="label">Address</span>
        </div>
        <div class="line"></div>
        <div class="step">
            <span class="circle">3</span>
            <span class="label">Payment</span>
        </div>
        <div class="line"></div>
        <div class="step">
            <span class="circle">4</span>
            <span class="label">Summary</span>
        </div>
    </div>
</header>

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

<div class="cart-item">
    <img src="<?= $img ?>">
    <div class="item-details">
        <h3><?= htmlspecialchars($row['Product_Name']) ?></h3>
        <p class="price">₹<?= number_format($price) ?></p>
        <p>Qty: <?= $qty ?></p>
        <p class="return">All issue easy returns</p>
        <a href="#" class="remove" data-id="<?= $row['Customize_Id'] ?>">✕ REMOVE</a>
    </div>
</div>

<div class="seller">
    <span>Free Delivery</span>
</div>

<?php endwhile; ?>
</div>

<!-- RIGHT -->
<?php
$discount = 14; // example
$total = $subtotal - $discount;
?>

<div class="cart-right">
<h3>Price Details</h3>

<div class="price-row">
    <span>Total Product Price</span>
    <span>₹<?= number_format($subtotal) ?></span>
</div>

<div class="price-row discount">
    <span>Total Discounts</span>
    <span>-₹<?= $discount ?></span>
</div>

<hr>

<div class="price-row total">
    <span>Order Total</span>
    <span>₹<?= number_format($total) ?></span>
</div>

<div class="discount-box">
    🎉 Yay! Your total discount is ₹<?= $discount ?>
</div>

<button class="continue-btn">Continue</button>
<p class="note">Clicking on "Continue" will not deduct any money</p>
</div>

</div>
</body>
</html>

<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "<p class='empty-msg'>Please login to see your cart.</p>";
    exit;
}

$uid = $_SESSION['User_Id'];

// Fetch cart ID
$getCart = mysqli_query($connection, "SELECT Cart_Id FROM cart WHERE User_Id='$uid'");
$cart = mysqli_fetch_assoc($getCart);

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

$subtotal = 0;
?>

<div class="cart-container">

<?php while ($row = mysqli_fetch_assoc($result)) :

    $imgData = base64_encode($row['Product_Image']);
    $imgSrc  = "data:image/jpeg;base64," . $imgData;
    $price   = $row['Price'];
    $qty     = $row['Quantity'];
    $subtotal += ($price * $qty);
?>

    <div class="cart-item" id="item-<?= $row['Customize_Id'] ?>">
        <img src="<?= $imgSrc ?>" class="cart-img" data-id="<?= $row['Customize_Id'] ?>">
        <div class="cart-info">
            <h4 class="item-title"><?= htmlspecialchars($row['Product_Name']) ?></h4>
            <p class="item-price"><?= $qty ?> x ₹<?= number_format($price) ?></p>
        </div>
        <span class="remove-btn" data-id="<?= $row['Customize_Id'] ?>">✕</span>
    </div>

    <hr>

<?php endwhile; ?>

    <div class="subtotal-box">
        <center><h3>Subtotal: <span id="subtotal-box">₹<?= number_format($subtotal) ?></span></h3></center>
    </div>
<hr>
    <div class="cart-actions">
        <a href="viewcart.php" class="view-cart-btn">View cart</a>
        <a href="checkout.php" class="checkout-btn">Checkout</a>
    </div>

</div>
<style>
.cart-container { 
    padding: 10px; 
}

.cart-item { 
    display: flex; 
    align-items: flex-start;  
    margin-bottom: 12px; 
}
.cart-img { 
    width: 80px; 
    height: 80px; 
    object-fit: cover; 
    border-radius: 6px; 
    margin-right: 12px;
    border: 1px solid #ddd;
}
.cart-info { flex: 1; }
.item-title { 
    font-size: 18px; 
    margin: 0; 
}
.item-price { 
    color: green; 
    font-weight: bold; 
    margin-top: 4px; 
}
.remove-btn { 
    cursor: pointer; 
    color: #999; 
    font-size: 22px; 
    padding: 5px; 
}
.remove-btn:hover { color: red; }
.subtotal-box { 
    margin: 20px 0 15px 0;       
    text-align: center;
    padding:10px;
}
.subtotal-box h3 {
    font-size: 30px;
}
.cart-actions { 
    display: flex; 
    gap: 15px;                   
    margin-top: 20px;            
}
.view-cart-btn, 
.checkout-btn { 
    flex: 1;                     
    text-align: center;
    padding: 12px 15px; 
    border-radius: 5px; 
    text-decoration: none; 
    font-weight: bold;
}
.view-cart-btn { 
    background: #000; 
    color: white; 
}

.checkout-btn { 
    background: #7e2626d5;         
    color: white;
}
</style>

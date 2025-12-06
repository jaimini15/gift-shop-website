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

// Fetch cart items
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

<?php while ($row = mysqli_fetch_assoc($result)) {

    $imgData = base64_encode($row['Product_Image']);
    $imgSrc = "data:image/jpeg;base64," . $imgData;

    $price = $row['Price'];
    $qty = $row['Quantity'];
    $subtotal += ($price * $qty);
?>

    <div class="cart-item" id="item-<?= $row['Customize_Id'] ?>">

        <img src="<?= $imgSrc ?>" class="cart-img" onclick="removeItem(<?= $row['Customize_Id'] ?>)">


        <div class="cart-info">
            <h4 class="item-title"><?= $row['Product_Name'] ?></h4>
            <p class="item-price"><?= $qty ?> × ₹<?= number_format($price) ?></p>
        </div>

        <!-- Delete button -->
        <span class="remove-btn" onclick="removeItem(<?= $row['Customize_Id'] ?>)">✕</span>

    </div>

    <hr>

<?php } ?>

    <div class="subtotal-box">
        <center><h3>Subtotal: <span id="subtotal-box">₹<?= number_format($subtotal) ?></span></h3></center>
    </div>

    <div class="cart-actions">
        <a href="viewcart.php" class="view-cart-btn">View cart</a>
        <a href="checkout.php" class="checkout-btn">Checkout</a>
    </div>

</div>


<!-- ==========================
      DELETE FUNCTION
============================= -->
<script>
function removeItem(id) {

    let itemDiv = document.getElementById("item-" + id);

    fetch("delete_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
    })
    .then(res => res.text())
    .then(data => {

        if (data.trim() === "success") {

            // Smooth fade-out
            itemDiv.style.opacity = "0";
            itemDiv.style.transition = "0.3s";

            setTimeout(() => {
                itemDiv.remove();
                updateSubtotal();
            }, 300);

        } else {
            alert("Error deleting item!");
        }
    });
}

function updateSubtotal() {

    let items = document.querySelectorAll(".item-price");
    let subtotal = 0;

    items.forEach(item => {
        let text = item.innerText;  // "1 × ₹550"
        let qty = parseInt(text.split("×")[0].trim());
        let price = parseInt(text.split("₹")[1].replace(/,/g, "").trim());
        subtotal += qty * price;
    });

    document.getElementById("subtotal-box").innerText = "₹" + subtotal.toLocaleString();
}
</script>


<!-- ==========================
          CSS (Same Design)
============================= -->
<style>
.cart-container { padding: 5px; }

.cart-item { 
    display: flex; 
    align-items: center; 
    margin-bottom: 7px; 
}

.cart-img { 
    width: 80px; 
    height: 80px; 
    object-fit: cover; 
    border-radius: 6px; 
    margin-right: 10px;
    border: 1px solid #ddd;
}

.cart-info { flex: 1; }

.item-title { 
    font-size: 20px; 
    margin: 0; 
}

.item-price { 
    color: green; 
    font-weight: bold; 
    margin: 3px 0 0; 
}

.remove-btn { 
    cursor: pointer; 
    color: #999; 
    font-size: 20px; 
    padding: 5px; 
}

.remove-btn:hover { color: red; }

.subtotal-box { 
    text-align: right; 
    font-size: 16px; 
    font-weight: bold; 
    margin: 10px 0; 
}

.cart-actions { 
    display: flex; 
    justify-content: space-between; 
    margin-top: 15px; 
}

.view-cart-btn { 
    background: black; 
    color: white; 
    padding: 10px 15px; 
    border-radius: 5px; 
    text-decoration: none; 
}

.checkout-btn { 
    background: #ff2f58; 
    color: white; 
    padding: 10px 15px; 
    border-radius: 5px; 
    text-decoration: none; 
}
</style>

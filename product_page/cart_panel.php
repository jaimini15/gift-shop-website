<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    echo "<p class='empty-msg'>Please login to see your cart.</p>";
    exit;
}

$userId = (int) $_SESSION['User_Id'];

// Fetch Cart ID
$stmt = mysqli_prepare($connection, "SELECT Cart_Id FROM cart WHERE User_Id = ?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $cartId);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$cartId) {
    echo "<p class='empty-msg'>Your cart is empty.</p>";
    exit;
}

// Fetch Cart Items
$stmt = mysqli_prepare(
    $connection,
    "SELECT ccd.*, pd.Product_Name, pd.Product_Image
     FROM customize_cart_details ccd
     JOIN product_details pd ON ccd.Product_Id = pd.Product_Id
     WHERE ccd.Cart_Id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $cartId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<p class='empty-msg'>Your cart is empty.</p>";
    exit;
}

$subtotal = 0;
?>

<div class="cart-container">

<?php while ($row = mysqli_fetch_assoc($result)) :
    $imgSrc  = "data:image/jpeg;base64," . base64_encode($row['Product_Image']);
    $subtotal += $row['Price'] * $row['Quantity'];
?>

<div class="cart-item" 
     id="item-<?= $row['Customize_Id'] ?>"
     data-total="<?= $row['Price'] * $row['Quantity'] ?>">
    <img src="<?= $imgSrc ?>" class="cart-img">
    <div class="cart-info">
        <h4><?= htmlspecialchars($row['Product_Name']) ?></h4>
        <p><?= $row['Quantity'] ?> × ₹<?= number_format($row['Price']) ?></p>
    </div>
    <span class="remove-btn" data-id="<?= $row['Customize_Id'] ?>">✕</span>
</div>
<hr>

<?php endwhile; ?>

<div class="subtotal-box">
    <h3>Subtotal: ₹<span id="cartSubtotal"><?= number_format($subtotal) ?></span></h3>
</div>


<div class="cart-actions">
    <a href="../view_cart/view_cart.php" class="view-cart-btn">View cart</a>
    <button class="checkout-btn" onclick="startRazorpay()">Checkout</button>

    
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
.view-cart-btn:hover {
    background: #222;
    color: white;
}

.checkout-btn:hover {
    background: #5f1d1d;
    color: white;
}

</style>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    document.querySelector('.checkout-btn').addEventListener('click', () => {
    alert("Button clicked ✅");
});

function startRazorpay() {
    alert("checkout");

    fetch("view_cart/create_razorpay_order.php") // 🔥 change path if needed
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert("Order create failed ❌");
            return;
        }

        var options = {
            key: data.key,
            amount: data.amount,
            currency: "INR",
            order_id: data.orderId,
            name: "My Store",
            description: "Cart Payment",

            handler: function (response) {

                fetch("view_cart/confirm_payment.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify(response)
                })
                .then(res => res.json())
                .then(result => {
                    if(result.success){
                        alert("Payment Successful ✅");
                        location.reload();
                    } else {
                        alert("Payment Failed ❌");
                    }
                });
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Razorpay error ❌ Check console");
    });
}
</script>


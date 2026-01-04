<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}

$userId  = $_SESSION['User_Id'];
$orderId = (int)($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid Order");
}

/* VERIFY ORDER BELONGS TO USER */
$check = mysqli_query($connection, "
    SELECT Order_Id 
    FROM `order`
    WHERE Order_Id = $orderId AND User_Id = $userId
");

if (mysqli_num_rows($check) === 0) {
    die("Unauthorized access");
}

/* FETCH PRODUCTS IN THIS ORDER */
$products = mysqli_query($connection, "
    SELECT DISTINCT
        pd.Product_Id,
        pd.Product_Name,
        pd.Product_Image
    FROM order_item oi
    JOIN product_details pd ON oi.Product_Id = pd.Product_Id
    WHERE oi.Order_Id = $orderId
");


$activePage = "orders";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Feedback</title>
    <link rel="stylesheet" href="../home page/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php include("account_layout.php"); ?>
<!-- <div class="account-wrapper"> -->


<div class="account-content">

<h2>Feedback</h2>

<form method="POST" action="submit_feedback.php">

<?php while ($p = mysqli_fetch_assoc($products)): ?>

    <div class="review-product-row">
        <img src="data:image/jpeg;base64,<?= base64_encode($p['Product_Image']) ?>">
        <h4><?= htmlspecialchars($p['Product_Name']) ?></h4>
    </div>
    
    <input type="hidden" name="product_id[]" value="<?= $p['Product_Id'] ?>">

    <div class="star-rating">
        <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio"
                   id="star<?= $i ?>_<?= $p['Product_Id'] ?>"
                   name="rating[<?= $p['Product_Id'] ?>]"
                   value="<?= $i ?>"
                   required>
            <label for="star<?= $i ?>_<?= $p['Product_Id'] ?>">★</label>
        <?php endfor; ?>
    </div>
<p style="color:#666;">We’d love to hear from you 💬</p>
    <textarea name="feedback[<?= $p['Product_Id'] ?>]"
        placeholder="Write your feedback..."
        style="width:100%;height:120px;margin-bottom:25px;"></textarea>

<?php endwhile; ?>

<button type="submit" class="btn"
    style="background:#8b3a3a;color:white;">
    Submit Reviews
</button>

</form>

</div>
<!-- </div> -->
        </div>
        </div>
<?php include("../home page/footer.php"); ?>

</body>
</html>

<style>
.review-product-row {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
}
.review-product-row img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
}

.star-rating {
    direction: rtl;
    display: inline-flex;      /* ⭐ THIS IS THE KEY */
    gap: 6px;                  /* space between stars */
    font-size: 30px;
    margin-bottom: 20px;
}

.star-rating input {
    display: none;
}
.star-rating label {
    color: #ddd;
    cursor: pointer;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #f5a623;
}
</style>

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
/* CHECK IF ALL PRODUCTS ARE ALREADY RATED */
$unratedCheck = mysqli_query($connection, "
    SELECT oi.Product_Id
    FROM order_item oi
    LEFT JOIN feedback_details fd 
        ON fd.Product_Id = oi.Product_Id 
        AND fd.User_Id = $userId
    WHERE oi.Order_Id = $orderId
      AND fd.Rating IS NULL
");

$hasUnratedProducts = mysqli_num_rows($unratedCheck) > 0;


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
<?php if ($hasUnratedProducts): ?>
    <div class="rating-warning">
         &#11088; Please rate all products in this order.<br>
    </div>
<?php endif; ?>

<h2>Feedback</h2>
<div id="ratingError" class="rating-warning" style="display:none;">
    &#11088; Please give star rating before submitting.
</div>
<form method="POST" action="submit_feedback.php" onsubmit="return validateRatings();" novalidate>

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
                   value="<?= $i ?>">
            <label for="star<?= $i ?>_<?= $p['Product_Id'] ?>">★</label>
        <?php endfor; ?>
    </div>
<p style="color:#666;">We'd love to hear from you </p>
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
        </div>
<?php include("../home page/footer.php"); ?>
<script>
function validateRatings() {
    const ratingGroups = document.querySelectorAll('.star-rating');
    let valid = true;

    ratingGroups.forEach(group => {
        const radios = group.querySelectorAll('input[type="radio"]');
        const checked = Array.from(radios).some(r => r.checked);

        if (!checked) {
            valid = false;
        }
    });

    if (!valid) {
        document.getElementById('ratingError').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false; 
    }

    return true; 
}
</script>

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
    display: inline-flex;      
    gap: 6px;                  
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
.rating-warning {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    color: #856404;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

</style>

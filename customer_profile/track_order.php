<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$orderId = $_GET['id'] ?? 0;
/* Fetch user address + area */
$userQ = mysqli_query(
    $connection,
    "SELECT 
        u.First_Name,
        u.Address,
        a.Area_Name,
        a.Pincode
     FROM user_details u
     JOIN area_details a ON u.Area_Id = a.Area_Id
     WHERE u.User_Id = '{$_SESSION['User_Id']}'"
);

$userAddress = mysqli_fetch_assoc($userQ);
/* Fetch order */
$orderQ = mysqli_query(
    $connection,
    "SELECT 
        o.*, 
        pd.Product_Image,
        pd.Product_Name,
        pd.Description
     FROM `order` o
     JOIN order_item oi ON o.Order_Id = oi.Order_Id
     JOIN product_details pd ON oi.Product_Id = pd.Product_Id
     WHERE o.Order_Id='$orderId'
     LIMIT 1"
);

$order = mysqli_fetch_assoc($orderQ);

/* Fetch delivery */
$deliveryQ = mysqli_query(
    $connection,
    "SELECT * FROM delivery_details WHERE Order_Id='$orderId'"
);
$delivery = mysqli_fetch_assoc($deliveryQ);

$status = $delivery['Delivery_Status'] ?? 'Ordered';

$stepMap = [
    'Ordered' => 1,
    'Packed' => 2,
    'Out of Delivery' => 3,
    'Delivered' => 4
];
$currentStep = $stepMap[$status] ?? 1;
?>
 <?php include("../home page/navbar.php"); ?>
 <title>Track Order|GiftShop</title>
 <!-- MAIN CSS -->
    <link rel="stylesheet" href="../home page/style.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
.track-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.track-header a {
    text-decoration:none;
    color:#7e2626d5;
    font-size:14px;
}
.track-header h3{
    color:#7e2626d5;
}
.product-preview {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.product-preview img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.product-info h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.product-info p {
    margin: 5px 0 0;
    font-size: 14px;
    color: #666;
    line-height: 1.4;
}


.progress-container {
    margin: 40px 0;
    position: relative;
}

.progress-line {
    display: flex;
    justify-content: space-between;
    position: relative;
    align-items: center;
}
.progress-bg {
    position: absolute;
    top: 7px;
    left: 7%;
    right: 7%;
    height: 3px;
    background: #ddd;
    z-index: 0;
}
.progress-fill {
    position: absolute;
    top: 7px;
    left: 7%;
    height: 3px;
    background: #067d62;
    z-index: 1;
    transition: width 0.5s ease;
}
.step {
    text-align: center;
    width: 25%;
    position: relative;
    z-index: 2;
    color: #888;
    font-size: 14px;
}
.circle {
    width: 14px;
    height: 14px;
    background: #ddd;
    border-radius: 50%;
    margin: 0 auto 8px;
}
.step.active,
.step.completed {
    color: #067d62;
    font-weight: 600;
}

.step.active .circle,
.step.completed .circle {
    background: #067d62;
}

.shipping-box {
    border:1px solid #7e2626d5;
    padding:15px;
    width:350px;
    margin-top:30px;
}

.shipping-box h4 {
    margin-bottom:8px;
    color:#7e2626d5;
}
.progress-fill {
    width: 0%;
    transition: width 0.8s ease-in-out;
}
.circle {
    transition: background 0.3s ease;
}
.progress-line {
    overflow: hidden;
}
/* PAGE WRAPPER */
.track-wrapper {
    width: 100%;
    min-height: 70vh;
    display: flex;
    justify-content: center;
    padding: 40px 0;
    background: white; 
}

/* BOX CARD */
.track-container {
    width: 900px;
    background: #fff;
    padding: 30px;
    border: 1px solid #7e2626d5 ;
    border-radius: 6px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
}


</style>
<div class="track-wrapper">
    <div class="track-container">

<div class="track-header">
    <h3>
        <?= $status == 'Delivered'
            ? 'Delivered'
            : 'Arriving soon' ?>
    </h3>

    <a href="orders.php">See all orders</a>
</div>

<div class="product-preview">
    <img src="data:image/jpeg;base64,<?= base64_encode($order['Product_Image']) ?>">

    <div class="product-info">
        <h4><?= htmlspecialchars($order['Product_Name']) ?></h4>
        <p><?= htmlspecialchars($order['Description']) ?></p>
    </div>
</div>


<!-- PROGRESS BAR -->
<div class="progress-container">
    <div class="progress-line">
        <div class="progress-bg"></div>
        <div class="progress-fill"></div>

        <?php
$labels = ['Ordered','Packed','Out for Delivery','Delivered'];
for ($i = 1; $i <= 4; $i++):
?>
    <div class="step">
        <div class="circle"></div>
        <span><?= $labels[$i-1] ?></span>
    </div>
<?php endfor; ?>

    </div>
</div>


<!-- SHIPPING ADDRESS -->
<div class="shipping-box">
    <h4>Shipping Address</h4>
    <p>
       <?= htmlspecialchars($userAddress['First_Name']) ?><br>
<?= htmlspecialchars($userAddress['Address']) ?><br>
<?= htmlspecialchars($userAddress['Area_Name']) ?> - <?= htmlspecialchars($userAddress['Pincode']) ?>
    </p>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {

    const progressFill = document.querySelector(".progress-fill");
    const steps = document.querySelectorAll(".step");
    const bgLine = document.querySelector(".progress-bg");

    const currentStep = <?= $currentStep ?>;

    const stepPositions = [];

    // Calculate circle center positions
    steps.forEach(step => {
        const circle = step.querySelector(".circle");
        const circleRect = circle.getBoundingClientRect();
        const bgRect = bgLine.getBoundingClientRect();

        const centerX = circleRect.left + circleRect.width / 2;
        stepPositions.push(centerX - bgRect.left);
    });

    const fullLineWidth = bgLine.offsetWidth;

    let index = 0;

    function animate() {
        if (index >= currentStep) return;

        let targetWidth;
        if (index === steps.length - 1 && currentStep === steps.length) {
            targetWidth = fullLineWidth;
        } else {
            targetWidth = stepPositions[index];
        }

        progressFill.style.width = targetWidth + "px";

        setTimeout(() => {
            steps[index].classList.add("completed");
            index++;
            setTimeout(animate, 600);
        }, 500);
    }

    animate();
});
</script>

    </div>
</div>

<?php include("../home page/footer.php"); ?>


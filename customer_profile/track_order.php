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
    "SELECT o.*, pd.Product_Image
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

/* Step mapping */
$stepMap = [
    'Ordered' => 1,
    'Packed' => 2,
    'Out of Delivery' => 3,
    'Delivered' => 4
];
$currentStep = $stepMap[$status] ?? 1;

include("account_layout.php");
?>

<style>
.track-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.track-header a {
    text-decoration:none;
    color:#007185;
    font-size:14px;
}

.product-preview img {
    width:80px;
    margin-bottom:15px;
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

/* FULL GREY LINE */
.progress-bg {
    position: absolute;
    top: 7px;
    left: 7%;
    right: 7%;
    height: 3px;
    background: #ddd;
    z-index: 0;
}

/* GREEN PROGRESS LINE */
.progress-fill {
    position: absolute;
    top: 7px;
    left: 7%;
    height: 3px;
    background: #067d62;
    z-index: 1;
    transition: width 0.5s ease;
}

/* STEP */
.step {
    text-align: center;
    width: 25%;
    position: relative;
    z-index: 2;
    color: #888;
    font-size: 14px;
}

/* CIRCLE */
.circle {
    width: 14px;
    height: 14px;
    background: #ddd;
    border-radius: 50%;
    margin: 0 auto 8px;
}

/* ACTIVE / COMPLETED */
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
    border:1px solid #ddd;
    padding:15px;
    width:350px;
    margin-top:30px;
}

.shipping-box h4 {
    margin-bottom:8px;
}
.progress-fill {
    width: 0%;
    transition: width 0.8s ease-in-out;
}
.circle {
    transition: background 0.3s ease;
}

</style>

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



</div> <!-- account-content -->
</div> <!-- account-wrapper -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const progressFill = document.querySelector(".progress-fill");
    const steps = document.querySelectorAll(".step");

    const currentStep = <?= $currentStep ?>;
    const totalSteps = steps.length;

    let stepIndex = 1;

    function animateStep() {
        if (stepIndex > currentStep) return;

        // 1️⃣ Move line first
        const percent = ((stepIndex - 1) / (totalSteps - 1)) * 100;
        progressFill.style.width = percent + "%";

        // 2️⃣ After line reaches, activate circle + text
        setTimeout(() => {

            // previous steps → completed
            for (let i = 0; i < stepIndex - 1; i++) {
                steps[i].classList.add("completed");
            }

            // current step → active
            steps[stepIndex - 1].classList.add("active");

            stepIndex++;
            setTimeout(animateStep, 600); // pause before next step

        }, 500); // wait until line touches circle
    }

    animateStep();
});
</script>


<?php include("../home page/footer.php"); ?>


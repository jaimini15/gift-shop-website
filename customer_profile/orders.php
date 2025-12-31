<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

/* USER NAME */
$profileUser = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT First_Name FROM user_details WHERE User_Id='$uid'")
);

/* DELIVERY TEXT FUNCTION (+3 DAYS) */
function getDeliveryText($orderDate) {
    $orderDate = new DateTime($orderDate);
    $deliveryDate = clone $orderDate;
    $deliveryDate->modify('+3 days');

    $today = new DateTime();
    $today->setTime(0,0,0);
    $deliveryDate->setTime(0,0,0);

    $diff = (int)$today->diff($deliveryDate)->format('%r%a');

    if ($diff === 0) {
        return "Arriving today";
    } elseif ($diff === 1) {
        return "Arriving tomorrow";
    } elseif ($diff > 1) {
        return "Arriving on " . $deliveryDate->format('d M Y');
    } else {
        return "Delivered on " . $deliveryDate->format('d M Y');
    }
}

/* FETCH ORDERS */
$orders = mysqli_query(
    $connection,
    "SELECT * FROM `order` WHERE User_Id='$uid' ORDER BY Order_Date DESC"
);

$activePage = "orders";
include("account_layout.php");
?>

<h2>My Orders</h2>

<?php if (mysqli_num_rows($orders) == 0): ?>

    <div class="empty-box">
        <p>No orders found!</p>
        <a href="../home page/index.php" class="btn">START GIFTING</a>
    </div>

<?php else: ?>

<?php while ($order = mysqli_fetch_assoc($orders)): ?>

<?php
$deliveryText = getDeliveryText($order['Order_Date']);

/* FETCH ORDER ITEMS WITH PRODUCT DATA */
$items = mysqli_query(
    $connection,
    "SELECT 
        oi.Quantity,
        oi.Price_Snapshot,
        pd.Product_Name,
        pd.Product_Image
     FROM order_item oi
     JOIN product_details pd ON oi.Product_Id = pd.Product_Id
     WHERE oi.Order_Id = '{$order['Order_Id']}'"
);
?>

<div class="order-card">

    <!-- TOP SUMMARY BAR -->
    <div class="order-summary">

        <div>
            <span class="label">ORDER PLACED</span>
            <span><?= date('d M Y', strtotime($order['Order_Date'])) ?></span>
        </div>

        <div>
            <span class="label">TOTAL</span>
            <span>₹<?= $order['Total_Amount'] ?? $order['Total'] ?></span>
        </div>

        <div>
            <span class="label">SHIP TO</span>
            <span><?= htmlspecialchars($profileUser['First_Name']) ?></span>
        </div>


    <!-- ORDER # LINE -->
   <div class="order-id invoice-wrapper">
    <span class="order-number">
        <span class="label">ORDER #</span>
        <span><?= $order['Order_Id'] ?></span>
    </span>

    <a
        href="invoice.php?order_id=<?= $order['Order_Id'] ?>"
        target="_blank"
        class="invoice-link"
    >
        View invoice
    </a>
</div>


    </div>

    <!-- DELIVERY TEXT -->
    <div class="delivery-text">
        <?= $deliveryText ?>
    </div>

    <!-- ORDER ITEMS -->
    <?php while ($item = mysqli_fetch_assoc($items)): ?>
        <div class="order-item">

            <div class="order-img">
                <img src="data:image/jpeg;base64,<?= base64_encode($item['Product_Image']) ?>">
            </div>

            <div class="item-info">
                <p class="item-name"><?= htmlspecialchars($item['Product_Name']) ?></p>
                <p class="item-price">₹<?= $item['Price_Snapshot'] ?></p>
            </div>

        </div>
    <?php endwhile; ?>

    <!-- ACTION -->
    <div class="order-actions">
        <a href="track_order.php?id=<?= $order['Order_Id'] ?>" class="track-btn">
            Track package
        </a>
    </div>

</div>



<?php endwhile; ?>
<?php endif; ?>

</div> <!-- account-content -->
</div> <!-- account-wrapper -->
<script>
document.addEventListener("click", function (e) {

    document.querySelectorAll(".invoice-dropdown").forEach(drop => {
        if (!drop.contains(e.target)) {
            drop.classList.remove("active");
        }
    });

    if (e.target.closest(".invoice-text")) {
        e.target.closest(".invoice-dropdown").classList.toggle("active");
    }
});
</script>

<?php include("../home page/footer.php"); ?>

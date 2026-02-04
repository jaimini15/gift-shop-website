<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

/* FETCH USER INFO WITH AREA */
$profileUser = mysqli_fetch_assoc(
    mysqli_query($connection, "
        SELECT u.First_Name, u.Address, a.Area_Name, a.Pincode
        FROM user_details u
        LEFT JOIN area_details a ON u.Area_Id = a.Area_Id
        WHERE u.User_Id = '$uid'
    ")
);

/* DELIVERY TEXT FUNCTION (+3 DAYS) */
function getDeliveryText($orderDate, $deliveryStatus, $deliveryDate = null) {
    if ($deliveryStatus === 'Delivered' && $deliveryDate) {
        return "Delivered on " . date('d M Y', strtotime($deliveryDate));
    }

    $orderDate = new DateTime($orderDate);
    $estimated = clone $orderDate;
    $estimated->modify('+3 days');
    $today = new DateTime('today');

    if ($today > $estimated) return "Arriving soon";
    $diff = $today->diff($estimated)->days;

    if ($diff === 0) return "Arriving today";
    if ($diff === 1) return "Arriving tomorrow";

    return "Arriving on " . $estimated->format('d M Y');
}

/* FETCH ORDER */
$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) die("Invalid Order ID");

$orderRes = mysqli_query($connection, "
    SELECT o.Order_Id, o.Order_Date, o.Total_Amount, o.Status,
           d.Delivery_Status, d.Delivery_Date
    FROM `order` o
    LEFT JOIN delivery_details d ON o.Order_Id = d.Order_Id
    WHERE o.Order_Id='$orderId' AND o.User_Id='$uid'
");
$order = mysqli_fetch_assoc($orderRes);
if (!$order) die("Invalid Order");

/* FETCH PAYMENT METHOD */
$payment = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT Payment_Method
    FROM payment_details
    WHERE Order_Id='{$order['Order_Id']}'
"));

/* FETCH ITEMS */
$itemRes = mysqli_query($connection, "
    SELECT 
        oi.Quantity,
        oi.Price_Snapshot,
        oi.Is_Hamper_Suggested,
        pd.Product_Name,
        pd.Product_Image
    FROM order_item oi
    JOIN product_details pd ON oi.Product_Id = pd.Product_Id
    WHERE oi.Order_Id='$orderId'
");

$items = [];
$subtotal = 0;
while ($row = mysqli_fetch_assoc($itemRes)) {
    $subtotal += $row['Quantity'] * $row['Price_Snapshot'];
    $items[] = $row;
}

$grandTotal = $order['Total_Amount'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Invoice | GiftShop</title>
<link rel="stylesheet" href="../home page/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
body{background:white;font-family:Arial}
.invoice-wrapper{padding:50px 0}
.invoice-container{width:1000px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:6px;
    border:1px solid #7e2626d5;
     box-shadow: 0px 4px 12px rgba(0,0,0,0.2);}

.invoice-header{display:flex;justify-content:space-between;align-items:center;color:#7e2626d5;}
.print-btn{background:#ffd814;border:none;padding:8px 18px;border-radius:20px;font-weight:600;cursor:pointer}

.order-meta{margin-top:6px;color:#555;font-size:14px}
.order-meta span{margin-right:15px} /* GAP between Order placed and Order # */

.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:20px;
    border:1px solid #ddd;
    padding:15px;
    margin-top:20px;
}
.info-grid h4{
    color:#7e2626d5;
}
.summary-row{display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px}
.summary-row.total{font-weight:bold;border-top:1px solid #ddd;padding-top:8px}

.product-box {
    border:1px solid #ddd;
    padding:15px;
    border-radius:6px;
    background:#fafafa;
    margin-bottom:20px;
}
.product-box img{
    width:80px;
    height:80px;
    object-fit:cover;
    border:1px solid #ddd;
}
.product-details-row {
    display:flex;
    align-items:center;
    gap:15px;
}
.product-details-row img {
    width:80px;
    height:80px;
    object-fit:cover;
    border:1px solid #ddd;
}

.product-info {
    flex:1;
}
.product-info{flex:1}
.product-delivery {
    font-weight:600;
    color:#007600;
    margin-bottom:10px; 
}

</style>
</head>

<body>

<div class="invoice-wrapper">
<div class="invoice-container">

<!-- HEADER -->
<div class="invoice-header">
    <h2>Order Summary</h2>
    <button class="print-btn" onclick="window.print()">Print</button>
</div>

<!-- ORDER META -->
<div class="order-meta">
    <span>Order placed <?= date("d F Y", strtotime($order['Order_Date'])) ?></span>
    <span>Order # <?= $order['Order_Id'] ?></span>
</div>

<!-- INFO GRID -->
<div class="info-grid">
    <div>
        <h4>Ship To</h4>
        <p>
            <?= htmlspecialchars($profileUser['First_Name']) ?><br>
            <?= htmlspecialchars($profileUser['Address']) ?><br>
            <?= htmlspecialchars($profileUser['Area_Name']) ?>, <?= htmlspecialchars($profileUser['Pincode']) ?>
        </p>
    </div>

    <div>
        <h4>Payment Method</h4>
        <p><?= htmlspecialchars($payment['Payment_Method'] ?? 'N/A') ?></p>
    </div>

    <div>
        <h4>Order Summary</h4>
        <div class="summary-row"><span>Subtotal</span><span>₹<?= number_format($subtotal,2) ?></span></div>
        <div class="summary-row"><span>Shipping</span><span>₹0.00</span></div>
        <div class="summary-row total"><span>Grand Total</span><span>₹<?= number_format($grandTotal,2) ?></span></div>
    </div>
</div>

<!-- ORDER ITEMS WITH ARRIVAL DATE -->
<div class="arrival-box">
<?php foreach ($items as $item) { 
    $deliveryText = getDeliveryText($order['Order_Date'], $order['Delivery_Status'] ?? '', $order['Delivery_Date'] ?? null);
?>
<div class="product-box">
    <!-- Delivery date row -->
    <div class="product-delivery"><?= $deliveryText ?></div>

    <!-- Product info row -->
    <div class="product-details-row">
        <img src="data:image/jpeg;base64,<?= base64_encode($item['Product_Image']) ?>">
        <div class="product-info">
            <strong><?= htmlspecialchars($item['Product_Name']) ?></strong>
            <?php if (!empty($item['Custom_Text'])) { ?>
                <br><em>Custom Text: <?= htmlspecialchars($item['Custom_Text']) ?></em>
            <?php } ?>
            <?php if (!empty($item['Is_Hamper_Suggested'])) { ?>
                <br><span style="color:green;font-weight:bold"> Hamper Selected</span>
            <?php } ?>
            <br>Qty: <?= $item['Quantity'] ?> | Price: ₹<?= number_format($item['Price_Snapshot'],2) ?>
        </div>
    </div>
</div>

<?php } ?>
</div>

</div>
</div>
</body>
</html>

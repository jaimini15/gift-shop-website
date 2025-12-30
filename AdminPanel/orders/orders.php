<?php
include(__DIR__ . '/../db.php');

/* ================= IMAGE STREAM HANDLER ================= */
if (isset($_GET['img'])) {
    $id = (int)$_GET['img'];

    $q = mysqli_query($connection, "SELECT Custom_Image FROM order_item WHERE Order_Item_Id = $id LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        $row = mysqli_fetch_assoc($q);

        if (!empty($row['Custom_Image'])) {
            header("Content-Type: image/jpeg");
            header("Content-Length: " . strlen($row['Custom_Image']));
            echo $row['Custom_Image'];
            exit;
        }
    }
    http_response_code(404);
    exit;
}
/* ======================================================== */

/* ============ UPDATE DELIVERY STATUS ==================== */
if (isset($_POST['set_packed'])) {
    $orderId = (int)$_POST['order_id'];

    $check = mysqli_query($connection,
        "SELECT Delivery_Id FROM delivery_details WHERE Order_Id = $orderId LIMIT 1");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($connection,
    "INSERT INTO delivery_details (Order_Id, Delivery_Address, Delivery_Status)
     VALUES ($orderId, '', 'Packed')");

    } else {
        mysqli_query($connection,
            "UPDATE delivery_details
             SET Delivery_Status = 'Packed'
             WHERE Order_Id = $orderId");
    }
}
/* ======================================================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body { background:#f4f6f9; font-family:Arial; }
        .content { margin-left:120px; padding:20px; margin-top:30px; }
        .card-box { background:#fff; padding:20px; border-radius:12px; }
        .order-header { background:#212529; color:#fff; padding:15px; border-radius:8px; }
        .product-img { width:60px; height:60px; object-fit:cover; border-radius:6px; }
        .badge-yes { background:#198754; }
        .badge-no { background:#dc3545; }
    </style>
</head>

<body>

<div class="content">
<div class="card-box">

<h2 class="fw-bold mb-4">Manage Orders</h2>

<?php
$orders = mysqli_query($connection, "SELECT * FROM `order` ORDER BY Order_Id DESC");

while ($order = mysqli_fetch_assoc($orders)) {

    $h = mysqli_query($connection,
        "SELECT Is_Hamper_Suggested FROM order_item 
         WHERE Order_Id = {$order['Order_Id']} LIMIT 1");
    $hr = mysqli_fetch_assoc($h);
    $isHamper = ($hr && $hr['Is_Hamper_Suggested'] == 1);

    $d = mysqli_query($connection,
        "SELECT Delivery_Status FROM delivery_details 
         WHERE Order_Id = {$order['Order_Id']} LIMIT 1");
    $dr = mysqli_fetch_assoc($d);
    $deliveryStatus = $dr['Delivery_Status'] ?? '';
?>

<div class="order-header mb-3">
    <div class="row mb-2">
        <div class="col-md-3"><strong>Order ID:</strong> <?= $order['Order_Id'] ?></div>
        <div class="col-md-3"><strong>User ID:</strong> <?= $order['User_Id'] ?></div>
        <div class="col-md-3"><strong>Date:</strong> <?= $order['Order_Date'] ?></div>
        <div class="col-md-3"><strong>Status:</strong> <?= $order['Status'] ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-md-3"><strong>Total Amount:</strong> ₹<?= number_format($order['Total_Amount'],2) ?></div>
        <div class="col-md-3">
            <strong>Hamper Suggested:</strong>
            <span class="badge <?= $isHamper ? 'badge-yes':'badge-no' ?>">
                <?= $isHamper ? 'Yes':'No' ?>
            </span>
        </div>
    </div>
</div>

<table class="table table-bordered mb-5">
<thead>
<tr>
    <th>Item ID</th>
    <th>Product ID</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Custom Text</th>
    <th>Custom Image</th>
    <th>Delivery Status</th>
</tr>
</thead>
<tbody>

<?php
$items = mysqli_query($connection,
    "SELECT * FROM order_item WHERE Order_Id = {$order['Order_Id']}");

while ($item = mysqli_fetch_assoc($items)) {
?>
<tr>
    <td><?= $item['Order_Item_Id'] ?></td>
    <td><?= $item['Product_Id'] ?></td>
    <td><?= $item['Quantity'] ?></td>
    <td>₹<?= number_format($item['Price_Snapshot'],2) ?></td>
    <td><?= $item['Custom_Text'] ?? 'N/A' ?></td>
    <td>
        <?php if (!empty($item['Custom_Image'])) { ?>
            <img src="orders.php?img=<?= $item['Order_Item_Id'] ?>" class="product-img">
        <?php } else { ?>
            <span class="text-muted">No Image</span>
        <?php } ?>
    </td>
    <td>
        <form method="post">
            <input type="hidden" name="order_id" value="<?= $order['Order_Id'] ?>">
            <select class="form-select form-select-sm"
                    onchange="this.form.submit()"
                    name="set_packed">
                <option value="">Select</option>
                <option value="Packed" <?= $deliveryStatus=='Packed'?'selected':'' ?>>Packed</option>
                <option disabled>Out of Delivery</option>
                <option disabled>Delivered</option>
            </select>
        </form>
    </td>
</tr>
<?php } ?>

</tbody>
</table>

<?php } ?>

</div>
</div>

</body>
</html>

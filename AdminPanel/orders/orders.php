<?php
// ================= START OUTPUT BUFFERING =================
ob_start();

// Include database connection
include(__DIR__ . '/../db.php');
if (isset($_POST['set_packed']) && $_POST['set_packed'] === 'Packed') {

    $orderId = (int) $_POST['order_id'];

    // Get user ID
    $oq = mysqli_query($connection,
        "SELECT User_Id FROM `order` WHERE Order_Id = $orderId LIMIT 1");
    $or = mysqli_fetch_assoc($oq);
    $userId = (int) ($or['User_Id'] ?? 0);

    // Get address & area
    $uq = mysqli_query($connection,
        "SELECT Area_Id, Address FROM user_details WHERE User_Id = $userId LIMIT 1");
    $ur = mysqli_fetch_assoc($uq);

    $areaId  = $ur['Area_Id'] ?? NULL;
    $address = mysqli_real_escape_string($connection, $ur['Address'] ?? '');

    // Check existing delivery record
    $check = mysqli_query($connection,
        "SELECT Delivery_Status FROM delivery_details WHERE Order_Id = $orderId LIMIT 1");
    $dr = mysqli_fetch_assoc($check);
    $currentStatus = $dr['Delivery_Status'] ?? '';

    if ($currentStatus !== 'Delivered') {
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($connection,
                "INSERT INTO delivery_details
                 (Order_Id, Area_Id, Delivery_Address, Delivery_Status)
                 VALUES ($orderId, $areaId, '$address', 'Packed')");
        } else {
            mysqli_query($connection,
                "UPDATE delivery_details
                 SET Area_Id = $areaId,
                     Delivery_Address = '$address',
                     Delivery_Status = 'Packed'
                 WHERE Order_Id = $orderId");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Orders - Admin Panel</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
 body { background: #f4f6f9; font-family: Arial, sans-serif; }
.content { margin-left: 0px; padding: 0px;  }
.card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.order-header { background:#212529; color:#fff; padding:15px; border-radius:8px; }
.product-img { width:60px; height:60px; object-fit:cover; border-radius:6px; }
.cat-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
.badge-yes { background:#198754; }
.badge-no { background:#dc3545; }
.date-row {
    background:#e9ecef;
    font-weight:bold;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}
</style>

</head>

<body>

<div class="content">
<div class="card-box">

<h2 class="fw-bold mb-4">Manage Orders</h2>

<?php
// ================= FETCH NEW ORDERS =================
$orders = mysqli_query($connection, "
    SELECT o.*, DATE(o.Order_Date) AS Order_Date_Only
    FROM `order` o
    LEFT JOIN delivery_details d 
        ON d.Order_Id = o.Order_Id
    WHERE d.Delivery_Status IS NULL
       OR d.Delivery_Status NOT IN ('Packed', 'Delivered')
    ORDER BY o.Order_Date DESC
");

if (mysqli_num_rows($orders) == 0) {
    echo '<div class="text-center text-muted fw-semibold py-5">
            No orders found
          </div>';
}

$lastDate = null;
?>

<?php while ($order = mysqli_fetch_assoc($orders)): ?>

<?php
    if ($lastDate !== $order['Order_Date_Only']) {
        echo '
        <div class="date-row">
            📅 ' . date("d-m-Y", strtotime($order['Order_Date_Only'])) . '
        </div>';
        $lastDate = $order['Order_Date_Only'];
    }

    // Fetch area
    $areaQ = mysqli_query($connection, "
        SELECT a.Area_Name
        FROM user_details u
        JOIN area_details a ON a.Area_Id = u.Area_Id
        WHERE u.User_Id = {$order['User_Id']}
        LIMIT 1
    ");
    $area = mysqli_fetch_assoc($areaQ);
    $areaName = $area['Area_Name'] ?? 'N/A';

    // Check hamper
    $h = mysqli_query($connection,
        "SELECT Is_Hamper_Suggested FROM order_item WHERE Order_Id = {$order['Order_Id']} LIMIT 1");
    $hr = mysqli_fetch_assoc($h);
    $isHamper = ($hr && $hr['Is_Hamper_Suggested'] == 1);
?>

<div class="order-header mb-3">
<div class="row mb-2">
    <div class="col-md-3"><strong>Order ID:</strong> <?= $order['Order_Id'] ?></div>
    <div class="col-md-3"><strong>User ID:</strong> <?= $order['User_Id'] ?></div>
    <div class="col-md-3"><strong>Date:</strong> <?= $order['Order_Date'] ?></div>
    <div class="col-md-3"><strong>Status:</strong> <?= $order['Status'] ?></div>
</div>

<div class="row mb-2">
    <div class="col-md-3"><strong>Total:</strong> ₹<?= number_format($order['Total_Amount'],2) ?></div>

    <div class="col-md-3">
        <strong>Hamper:</strong>
        <span class="badge <?= $isHamper ? 'badge-yes' : 'badge-no' ?>">
            <?= $isHamper ? 'Yes' : 'No' ?>
        </span>
    </div>

    <div class="col-md-3">
        <strong>Area:</strong> <?= $areaName ?>
    </div>

    <div class="col-md-3">
        <form method="post">
            <input type="hidden" name="order_id" value="<?= $order['Order_Id'] ?>">
            <select name="set_packed"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()">
                <option value="">Select Status</option>
                <option value="Packed">Packed</option>
                <option disabled>Out for Delivery</option>
                <option disabled>Delivered</option>
            </select>
        </form>
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
</tr>
</thead>
<tbody>

<?php
$items = mysqli_query($connection,
    "SELECT * FROM order_item WHERE Order_Id = {$order['Order_Id']}");
while ($item = mysqli_fetch_assoc($items)):
?>
<tr>
    <td><?= $item['Order_Item_Id'] ?></td>
    <td><?= $item['Product_Id'] ?></td>
    <td><?= $item['Quantity'] ?></td>
    <td>₹<?= number_format($item['Price_Snapshot'],2) ?></td>
    <td><?= $item['Custom_Text'] ?? 'N/A' ?></td>
    <td>
        <?php if (!empty($item['Custom_Image'])): ?>
          <?php if (!empty($item['Custom_Image'])): ?>
    <img src="../<?= $item['Custom_Image'] ?>" class="product-img">
<?php else: ?>
    <span class="text-muted">No Image</span>
<?php endif; ?>

        <?php else: ?>
            <span class="text-muted">No Image</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<?php endwhile; ?>

</div>
</div>

</body>
</html>

<?php
ob_end_flush();
?>

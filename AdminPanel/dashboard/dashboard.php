<?php
if (!isset($_SESSION)) session_start();
include(__DIR__ . '/../db.php');

/* ================= TOTAL CUSTOMERS ================= */
$totalUsersQuery = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total 
     FROM user_details 
     WHERE User_Role='CUSTOMER'"
);
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];

/* ================= TOTAL PRODUCTS ================= */
$totalProductsQuery = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total 
     FROM product_details"
);
$totalProducts = mysqli_fetch_assoc($totalProductsQuery)['total'];

/* ================= TOTAL ORDERS ================= */
$totalOrdersQuery = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total 
     FROM `order`"
);
$totalOrders = mysqli_fetch_assoc($totalOrdersQuery)['total'];

/* ================= PENDING DELIVERIES ================= */
$pendingDeliveryQuery = mysqli_query(
    $connection,
    "
    SELECT COUNT(*) AS total
    FROM `order` o
    LEFT JOIN delivery_details d 
        ON o.Order_Id = d.Order_Id
    WHERE d.Delivery_Id IS NULL
       OR d.Delivery_Status != 'Delivered'
    "
);
$pendingDelivery = mysqli_fetch_assoc($pendingDeliveryQuery)['total'];

/* ================= RECENT ORDERS ================= */
$recentOrdersQuery = mysqli_query(
    $connection,
    "
    SELECT 
        o.Order_Id,
        o.Status,
        o.Total_Amount,
        u.First_Name,
        u.Last_Name
    FROM `order` o
    LEFT JOIN user_details u 
        ON o.User_Id = u.User_Id
    ORDER BY o.Order_Date DESC
    LIMIT 5
    "
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GiftShop Admin Panel</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}
.content {
    margin-left: 120px;
    padding: 20px;
    padding-top: 40px;
}
@media (max-width: 768px) {
    .content {
        margin-left: 0 !important;
        padding-top: 100px;
    }
}
.card-box {
    border-radius: 12px;
    padding: 20px;
    background: #fff;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    text-align: center;
}
table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}
</style>
</head>

<body>

<div class="content">

<h2 class="fw-bold mb-4">Dashboard Overview</h2>

<div class="row g-4">

<div class="col-md-3 col-6">
    <div class="card-box">
        <h5><i class="fa-solid fa-users"></i> Total Users</h5>
        <h3><?= $totalUsers ?></h3>
    </div>
</div>

<div class="col-md-3 col-6">
    <div class="card-box">
        <h5><i class="fa-solid fa-box"></i> Total Products</h5>
        <h3><?= $totalProducts ?></h3>
    </div>
</div>

<div class="col-md-3 col-6">
    <div class="card-box">
        <h5><i class="fa-solid fa-cart-shopping"></i> Orders</h5>
        <h3><?= $totalOrders ?></h3>
    </div>
</div>

<div class="col-md-3 col-6">
    <div class="card-box">
        <h5><i class="fa-solid fa-truck"></i> Pending Delivery</h5>
        <h3><?= $pendingDelivery ?></h3>
    </div>
</div>

</div>

<!-- ================= RECENT ORDERS ================= -->
<div class="mt-5 card-box">
<h5 class="fw-bold">Recent Orders</h5>

<table class="table table-bordered table-striped mt-3">
<thead class="table-dark">
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Status</th>
    <th>Amount</th>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($recentOrdersQuery) == 0) { ?>
<tr>
    <td colspan="4" class="text-center text-muted">No recent orders</td>
</tr>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($recentOrdersQuery)) { ?>
<tr>
    <td><?= $row['Order_Id'] ?></td>
    <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
    <td><?= $row['Status'] ?></td>
    <td>₹<?= number_format($row['Total_Amount'], 2) ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

</div>

</body>
</html>

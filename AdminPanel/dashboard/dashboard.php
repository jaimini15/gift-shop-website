<?php
if (!isset($_SESSION)) session_start();
include(__DIR__ . '/../db.php');

// TOTAL Customer
$totalUsersQuery = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total FROM user_details WHERE User_Role='CUSTOMER'"
);
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];

// TOTAL PRODUCTS
$totalProductsQuery = mysqli_query($connection, "SELECT COUNT(*) AS total FROM product_details");
$totalProducts = mysqli_fetch_assoc($totalProductsQuery)['total'];

// STATIC VALUES
$totalOrders = 56;
$pendingDelivery = 12;
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

    /* Default (Desktop) */
    .content {
      margin-left: 120px;
      padding: 20px;
      padding-top: 40px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding-top: 100px; /* space for fixed header */
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
        <tr><td>101</td><td>John</td><td>Delivered</td><td>1500</td></tr>
        <tr><td>102</td><td>Asha</td><td>Pending</td><td>850</td></tr>
      </tbody>
    </table>
  </div>

</div>

</body>
</html>

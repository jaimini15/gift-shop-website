<?php include "layout.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GiftShop Admin Panel</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <style>
    body {
      background: #f4f6f9;
      font-family: Arial, sans-serif;
    }

    /* HEADER */
    .header {
      height: 70px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: fixed;
      top: 0;
      left: 260px;
      right: 0;
      display: flex;
      align-items: center;
      padding: 0 30px;
      justify-content: space-between;
      z-index: 100;
    }

    /* SIDEBAR */
    .sidebar {
      width: 260px;
      height: 100vh;
      background: #343a40;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 80px;
      color: #fff;
      overflow-y: auto;
    }

    .sidebar a {
      padding: 12px 20px;
      display: block;
      color: #fff;
      font-size: 16px;
      text-decoration: none;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a:hover, 
    .sidebar a.active {
      background: #495057;
    }

    .content {
    margin-left: 260px;
    padding: 20px;
    padding-top: 30px; /* smaller space below fixed header */
}


    .card-box {
      border-radius: 12px;
      padding: 20px;
      background: #fff;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

  </style>

</head>

<body>

<!-- HEADER -->
<!-- <div class="header">
  <div class="logo fw-bold fs-4">
    <i class="fa-solid fa-gift text-danger"></i> GiftShop Admin
  </div>
  <div>
    <i class="fa-solid fa-user-shield"></i> Admin
  </div>
</div> -->

<!-- SIDEBAR -->
<!-- <div class="sidebar">

  <a href="#" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="#"><i class="fa-solid fa-users"></i> Users</a>
  <a href="category/categories.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
  <a href="products/products.php"><i class="fa-solid fa-box"></i> Products</a>
  <a href="#"><i class="fa-solid fa-boxes-packing"></i> Stock</a>
  <a href="#"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
  <a href="#"><i class="fa-solid fa-credit-card"></i> Payments</a>
  <a href="#"><i class="fa-solid fa-truck"></i> Delivery</a>
  <a href="#"><i class="fa-solid fa-comments"></i> Feedback</a>
  <a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>

</div> -->

<!-- MAIN CONTENT -->
<div class="content">
  
  <h2 class="fw-bold mb-0">Dashboard Overview</h2>

  <!-- Dashboard Cards -->
  <div class="row g-4">

    <div class="col-md-3">
      <div class="card-box text-center">
        <h4><i class="fa-solid fa-users"></i> Total Users</h4>
        <p>124</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-box text-center">
        <h4><i class="fa-solid fa-box"></i> Total Products</h4>
        <p>78</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-box text-center">
        <h4><i class="fa-solid fa-cart-shopping"></i> Orders</h4>
        <p>56</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-box text-center">
        <h4><i class="fa-solid fa-truck"></i> Pending Delivery</h4>
        <p>12</p>
      </div>
    </div>

  </div>

  <!-- Orders Table Only -->
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

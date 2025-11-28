<?php
// Optional: Start session
if(!isset($_SESSION)) session_start();

// Page loader
$page = isset($page) ? $page : "dashboard.php";
?>
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

    /* CONTENT AREA */
    .content {
      margin-left: 260px;
      padding: 20px;
      margin-top: 90px;
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
<div class="header">
  <div class="logo fw-bold fs-4">
    <i class="fa-solid fa-gift text-danger"></i> GiftShop Admin
  </div>
  <div>
    <i class="fa-solid fa-user-shield"></i> Admin
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">

  <a href="admin.php" class="<?php if($page=='dashboard.php') echo 'active'; ?>">
    <i class="fa-solid fa-chart-line"></i> Dashboard
  </a>

  <a href="users/users.php">
    <i class="fa-solid fa-users"></i> Users
  </a>

  <a href="category/categories.php">
    <i class="fa-solid fa-layer-group"></i> Categories
  </a>

  <a href="products/products.php">
    <i class="fa-solid fa-box"></i> Products
  </a>

  <a href="stock/stock.php">
    <i class="fa-solid fa-boxes-packing"></i> Stock
  </a>

  <a href="orders/orders.php">
    <i class="fa-solid fa-cart-shopping"></i> Orders
  </a>

  <a href="payments/payments.php">
    <i class="fa-solid fa-credit-card"></i> Payments
  </a>

  <a href="delivery/delivery.php">
    <i class="fa-solid fa-truck"></i> Delivery
  </a>

  <a href="feedback/feedback.php">
    <i class="fa-solid fa-comments"></i> Feedback
  </a>

  <a href="logout.php">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
  </a>

</div>

<!-- MAIN CONTENT -->
<div class="content">
  <?php include($page); ?>
</div>

</body>
</html>

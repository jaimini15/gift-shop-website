<?php
include __DIR__ . "/../session_protect.php";
 // Correct path of session_protect.php
?> 
<?php
include "../session_protect.php";  // Correct path to db.php
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

    .content {
    margin-left: 120px;
    padding: 20px;
    padding-top: 40px; /* smaller space below fixed header */
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

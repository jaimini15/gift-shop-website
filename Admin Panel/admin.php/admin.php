<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <style>
    body {
      background: #f4f6f9;
    }
    .sidebar {
      width: 260px;
      height: 100vh;
      background: #343a40;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 80px;
      color: #fff;
    }
    .sidebar a {
      padding: 12px 20px;
      display: block;
      color: #fff;
      text-decoration: none;
      font-size: 16px;
    }
    .sidebar a:hover {
      background: #495057;
    }
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
    }
    .content {
      margin-left: 260px;
      margin-top: 90px;
      padding: 20px;
    }
    .card-box {
      border-radius: 12px;
      padding: 20px;
      background: #fff;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }
    .logo {
      font-size: 22px;
      font-weight: bold;
      color: #343a40;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="header">
    <div class="logo">GiftShop Admin Panel</div>
    <div>Admin</div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <a href="#">Dashboard</a>
    <a href="#">Users</a>
    <a href="#">Categories</a>
    <a href="#">Products</a>
    <a href="#">Stock</a>
    <a href="#">Orders</a>
    <a href="#">Payments</a>
    <a href="#">Delivery</a>
    <a href="#">Feedback</a>
    <a href="#">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>Dashboard Overview</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3">
        <div class="card-box text-center">
          <h4>Total Users</h4>
          <p>124</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box text-center">
          <h4>Total Products</h4>
          <p>78</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box text-center">
          <h4>Orders</h4>
          <p>56</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box text-center">
          <h4>Pending Delivery</h4>
          <p>12</p>
        </div>
      </div>
    </div>

    <h3 class="mt-5">Quick Tables</h3>

    <div class="mt-3 card-box">
      <h5>Recent Users</h5>
      <table class="table table-bordered table-striped mt-2">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>1</td><td>John Doe</td><td>john@mail.com</td><td>Customer</td></tr>
          <tr><td>2</td><td>Asha Patel</td><td>asha@mail.com</td><td>Admin</td></tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 card-box">
      <h5>Recent Orders</h5>
      <table class="table table-bordered table-striped mt-2">
        <thead>
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

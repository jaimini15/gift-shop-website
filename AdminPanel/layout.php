<?php
// Start session if not already started
if (!isset($_SESSION)) session_start();

// Default page if $page_name is not set
$page_name = isset($page_name) ? $page_name : 'dashboard';

// Map page names to actual PHP files
$page_files = [
    'dashboard' => 'dashboard/dashboard.php',
    'users'     => 'users/users.php',
    'categories'=> 'category/categories.php',
    'products'  => 'products/products.php',
    'stock'     => 'stock/stock.php',
    'orders'    => 'orders/orders.php',
    'payments'  => 'payments/payments.php',
    'delivery'  => 'delivery/delivery.php',
    'feedback'  => 'feedback/feedback.php',
];

$page = isset($page_files[$page_name]) ? $page_files[$page_name] : $page_files['dashboard'];
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
    position: fixed;
    top: 0;
    left: 260px; /* same as sidebar width */
    right: 0;
    height: 70px;
    line-height: 70px; /* vertically center text */
    padding: 0 20px;
    z-index: 100;
    background: #fff;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* CONTENT AREA */
.content {
    margin-left: 260px; /* sidebar width */
    padding: 20px;
    padding-top: 20px; /* instead of margin-top */
}




    /* SIDEBAR */
    .sidebar {
        width: 260px;
        height: 100vh;
        background: #343a40;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 60px;
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

   

    /* inside dashboard.php or a specific CSS file */
.page-container {
    margin-top: 0;
    padding-top: 0;
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
    <a href="dashboard/dashboard.php" class="<?= ($page_name=='dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>

    <a href="users/users.php" class="<?= ($page_name=='users') ? 'active' : '' ?>">
        <i class="fa-solid fa-users"></i> Users
    </a>

    <a href="category/categories.php" class="<?= ($page_name=='categories') ? 'active' : '' ?>">
        <i class="fa-solid fa-layer-group"></i> Categories
    </a>

    <a href="products/products.php" class="<?= ($page_name=='products') ? 'active' : '' ?>">
        <i class="fa-solid fa-box"></i> Products
    </a>

    <a href="stock/stock.php" class="<?= ($page_name=='stock') ? 'active' : '' ?>">
        <i class="fa-solid fa-boxes-packing"></i> Stock
    </a>

    <a href="orders/orders.php" class="<?= ($page_name=='orders') ? 'active' : '' ?>">
        <i class="fa-solid fa-cart-shopping"></i> Orders
    </a>

    <a href="payments/payments.php" class="<?= ($page_name=='payments') ? 'active' : '' ?>">
        <i class="fa-solid fa-credit-card"></i> Payments
    </a>

    <a href="delivery/delivery.php" class="<?= ($page_name=='delivery') ? 'active' : '' ?>">
        <i class="fa-solid fa-truck"></i> Delivery
    </a>

    <a href="feedback/feedback.php" class="<?= ($page_name=='feedback') ? 'active' : '' ?>">
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

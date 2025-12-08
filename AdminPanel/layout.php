<?php
if (!isset($_SESSION))
    session_start();

$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';

$allowed = [
    'dashboard' => 'dashboard/dashboard.php',
    'users' => 'users/users.php',
    'categories' => 'category/categories.php',
    'products' => 'products/products.php',
    'stock' => 'stock/stock.php',
    'orders' => 'orders/orders.php',
    'payments' => 'payments/payments.php',
    'delivery' => 'delivery/delivery.php',
    'feedback' => 'feedback/feedback.php',
    'admin_profile' => 'admin/admin_profile.php',
    'delivery_boys' => 'delivery_boy/delivery_boys.php',
];

$page = isset($allowed[$view]) ? $allowed[$view] : $allowed['dashboard'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GiftShop Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 70px;
            color: white;
            transition: transform .3s ease-in-out;
        }

        .sidebar a {
            padding: 12px 20px;
            display: block;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #495057;
        }

        .sidebar a i {
            margin-right: 10px;
        }
        .header {
            height: 70px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding-left: 260px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-right: 20px;
            z-index: 1000;
        }
        .menu-btn {
            display: none;
            font-size: 26px;
            cursor: pointer;
            margin-right: 15px;
        }
        .content {
            margin-left: 260px;
            padding: 90px 20px 20px;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {

            .menu-btn {
                display: block;
            }

            .header {
                padding-left: 0;
            }

            .sidebar {
                transform: translateX(-260px);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="d-flex align-items-center">
            <!-- MOBILE TOGGLE BUTTON -->
            <i class="fa-solid fa-bars menu-btn" onclick="toggleSidebar()"></i>

            <div class="fw-bold fs-4">
                <i class="fa-solid fa-gift text-danger"></i> GiftShop Admin
            </div>
        </div>

        <!-- FIXED DROPDOWN -->
        <div class="dropdown">
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-user-shield"></i> Admin
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="layout.php?view=admin_profile">
                        <i class="fa-solid fa-id-badge"></i> Profile
                    </a></li>
                <li><a class="dropdown-item" href="admin_login/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a></li>
            </ul>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <a href="layout.php?view=dashboard" class="<?= ($view == 'dashboard') ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="layout.php?view=users" class="<?= ($view == 'users') ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Users</a>
        <a href="layout.php?view=categories" class="<?= ($view == 'categories') ? 'active' : '' ?>"><i class="fa-solid fa-layer-group"></i> Categories</a>
        <a href="layout.php?view=products" class="<?= ($view == 'products') ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> Products</a>
        <a href="layout.php?view=stock" class="<?= ($view == 'stock') ? 'active' : '' ?>"><i class="fa-solid fa-boxes-packing"></i> Stock</a>
        <a href="layout.php?view=orders" class="<?= ($view == 'orders') ? 'active' : '' ?>"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="layout.php?view=payments" class="<?= ($view == 'payments') ? 'active' : '' ?>"><i class="fa-solid fa-credit-card"></i> Payments</a>
        <a href="layout.php?view=delivery" class="<?= ($view == 'delivery') ? 'active' : '' ?>"><i class="fa-solid fa-truck"></i> Delivery</a>
        <a href="layout.php?view=feedback" class="<?= ($view == 'feedback') ? 'active' : '' ?>"><i class="fa-solid fa-comments"></i> Feedback</a>
        <a href="layout.php?view=delivery_boys" class="<?= ($view == 'delivery_boys') ? 'active' : '' ?>"><i class="fa-solid fa-motorcycle"></i> Delivery Boys</a>

    </div>

    <!-- PAGE CONTENT -->
    <div class="content">
        <?php include($page); ?>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

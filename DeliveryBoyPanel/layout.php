<?php
if (!isset($_SESSION))
    session_start();

// Default view
$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';
// Allowed pages
$allowed = [
    'dashboard' => 'dashboard/dashboard.php',
    'assigned_orders' => 'orders/assigned_orders.php',
    'Complete_deliveries' => 'complete_deliveries/complete_deliveries.php',
    'profile' => 'deliveryboy/deliveryboy_profile.php',
];
$page = isset($allowed[$view]) ? $allowed[$view] : $allowed['dashboard'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Boy Panel</title>

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
        }
        .sidebar a {
            padding: 12px 20px;
            display: block;
            color: #ffffff;
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

        .content {
            margin-left: 260px;
            padding: 90px 20px 20px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">

        <div class="fw-bold fs-4">
            <i class="fa-solid fa-motorcycle text-primary"></i> Delivery Boy Panel
        </div>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-user"></i>
                <?= isset($_SESSION['delivery_boy_name']) ? $_SESSION['delivery_boy_name'] : "Delivery Boy" ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <a class="dropdown-item" href="/GitHub/gift-shop-website/DeliveryBoyPanel/layout.php?view=profile">
                        <i class="fa-solid fa-id-card"></i> Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="deliveryboy_login/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>

        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <a href="layout.php?view=dashboard" class="<?= ($view == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>

        <a href="layout.php?view=assigned_orders" class="<?= ($view == 'assigned_orders') ? 'active' : '' ?>">
            <i class="fa-solid fa-box"></i> Assigned Orders
        </a>

        <a href="layout.php?view=complete_deliveries" class="<?= ($view == 'complete_deliveries') ? 'active' : '' ?>">
            <i class="fa-solid fa-sync"></i> Complete deliveries
        </a>


    </div>

    <!-- PAGE CONTENT -->
    <div class="content">
        <?php include($page); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
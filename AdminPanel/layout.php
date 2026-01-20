<?php
ob_start();
if (!isset($_SESSION))
    session_start();

/* ================= VIEW ROUTING ================= */
$view = $_GET['view'] ?? 'dashboard';

$allowed = [
    'dashboard' => 'dashboard/dashboard.php',
    'users' => 'users/users.php',
    'profile' => 'admin/edit_admin_profile.php',
    'categories' => 'category/categories.php',
    'products' => 'products/products.php',
    'stock' => 'stock/stock.php',
    'orders' => 'orders/orders.php',
    'payments' => 'payments/payments.php',
    'delivery' => 'delivery/delivery.php',
    'feedback' => 'feedback/feedback.php',
    'delivery_boys' => 'delivery_boy/delivery_boys.php',
    'admin_profile' => 'admin/admin_profile.php',
];

$page = $allowed[$view] ?? $allowed['dashboard'];
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GiftShop Admin Panel</title>


    <!-- MAIN SITE CSS -->
    <link rel="stylesheet" href="../home page/style.css">

    <!-- ACCOUNT PANEL CSS (IMPORTANT) -->
    <link rel="stylesheet" href="account.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* =====================
   GLOBAL
===================== */
        body {
            background: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* =====================
   HEADER
===================== */
        .admin-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 0 30px;
            z-index: 1000;
            font-size: 20px;
            font-weight: 700;
        }

        /* =====================
   MAIN WRAPPER
===================== */
        .account-wrapper {
            max-width: 1400px;
            /* WIDER LAYOUT */
            margin: 30px auto 40px;
            display: flex;
            gap: 25px;
            min-height: 600px;
        }

        /* =====================
   LEFT SIDEBAR (SMALLER)
===================== */
        .account-sidebar {
            width: 300px;
            min-width: 300px;
            /* 🔒 LOCK WIDTH */
            max-width: 300px;
            /* 🔒 LOCK WIDTH */
            flex-shrink: 0;
            /* ❌ DO NOT SHRINK */

            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #7e2626d5;
            box-shadow: 2px 5px 10px rgba(0, 0, 0, 0.2);
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
        }


        .sidebar-user {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #111827;
        }

        .account-sidebar a {
            text-decoration: none;
            color: #374151;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 10px;
            font-size: 15px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .account-sidebar a:hover {
            background: #f3f4f6;
        }

        .account-sidebar a.active {
            background: #7e2626d5;
            color: #ffffff;
            font-weight: 600;
        }

        /* =====================
   RIGHT CONTENT (WIDER + NORMAL TEXT)
===================== */
        .account-content {
            flex: 1;
            min-width: 0;
            /* 🔑 REQUIRED for flex layouts */
            overflow-x: auto;
            /* Horizontal scroll if needed */

            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #7e2626d5;
            box-shadow: 2px 5px 10px rgba(0, 0, 0, 0.2);
            padding: 35px;
            min-height: 600px;

            font-size: 15px;
            line-height: 1.6;

            
        }


        /* Prevent small inherited fonts */
        .account-content * {
            font-size: inherit;
        }

        .account-content table {
            width: 100%;
            table-layout: auto;
        }

        /* =====================
   RESPONSIVE
===================== */
        @media(max-width:900px) {
            .account-wrapper {
                flex-direction: column;
            }

            .account-sidebar {
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
            }

            .account-sidebar a {
                white-space: nowrap;
                margin-right: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <?php include("../home page/navbar.php"); ?>

    <!-- ================= ACCOUNT LAYOUT ================= -->
    <div class="account-wrapper">

        <!-- ===== LEFT PANEL ===== -->
        <div class="account-sidebar">

            <div class="sidebar-user">
                Hello, Admin 👋
            </div>
            <a href="admin_profile_main.php" class="<?= $view == 'account' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> My Account
            </a>
            <a href="layout.php?view=profile" class="<?=$view == 'profile' ? 'active' : '' ?>">
                <i class="fa-solid fa-user"></i> My Profile
            </a>
            <a href="layout.php?view=dashboard" class="<?= $view == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>

            <a href="layout.php?view=users" class="<?= $view == 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Users
            </a>

            <a href="layout.php?view=categories" class="<?= $view == 'categories' ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>

            <a href="layout.php?view=products" class="<?= $view == 'products' ? 'active' : '' ?>">
                <i class="fa-solid fa-box"></i> Products
            </a>

            <a href="layout.php?view=stock" class="<?= $view == 'stock' ? 'active' : '' ?>">
                <i class="fa-solid fa-boxes-packing"></i> Stock
            </a>

            <a href="layout.php?view=orders" class="<?= $view == 'orders' ? 'active' : '' ?>">
                <i class="fa-solid fa-cart-shopping"></i> Orders
            </a>

            <a href="layout.php?view=payments" class="<?= $view == 'payments' ? 'active' : '' ?>">
                <i class="fa-solid fa-credit-card"></i> Payments
            </a>

            <a href="layout.php?view=delivery" class="<?= $view == 'delivery' ? 'active' : '' ?>">
                <i class="fa-solid fa-truck"></i> Delivery
            </a>

            <a href="layout.php?view=delivery_boys" class="<?= $view == 'delivery_boys' ? 'active' : '' ?>">
                <i class="fa-solid fa-motorcycle"></i> Delivery Boys
            </a>

            <a href="layout.php?view=feedback" class="<?= $view == 'feedback' ? 'active' : '' ?>">
                <i class="fa-solid fa-comments"></i> Feedback
            </a>

        </div>

        <!-- ===== RIGHT PANEL ===== -->
        <div class="account-content">
            <?php include($page); ?>
        </div>

    </div>
    <?php require_once '../home page/footer.php'; ?>
</body>

</html>
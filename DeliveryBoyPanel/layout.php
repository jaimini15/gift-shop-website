<?php
ob_start();
if (!isset($_SESSION)) {
    session_start();
}

/* ================= VIEW ROUTING ================= */
$view = $_GET['view'] ?? 'dashboard';

$allowed = [
    'dashboard' => __DIR__ . '/dashboard/dashboard.php',
    'assigned_orders' => __DIR__ . '/orders/assigned_orders.php',
    'completed_deliveries' => __DIR__ . '/complete_deliveries/completed_deliveries.php',
    'profile' => __DIR__ . '/deliveryboy/deliveryboy_profile.php',
    'account' => __DIR__ . '/deliveryboy_profile_main.php', // ✅ FIX
];

$page = $allowed[$view] ?? $allowed['dashboard'];

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Boy Panel | GiftShop</title>

    <!-- MAIN SITE CSS -->
    <link rel="stylesheet" href="../home page/style.css">

    <!-- ACCOUNT PANEL CSS -->
    <link rel="stylesheet" href="../AdminPanel/account.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body {
            background: #ffffff;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .account-wrapper {
            max-width: 1400px;
            margin: 30px auto 40px;
            display: flex;
            gap: 25px;
            min-height: 600px;
            background: #ffffff;
        }

        .account-sidebar {
            width: 300px;
            min-width: 300px;
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
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .account-sidebar a:hover {
            background: #f3f4f6;
        }

        .account-sidebar a.active {
            background: #7e2626d5;
            color: #ffffff;
            font-weight: 600;
        }

        .account-content {
            flex: 1;
            min-width: 0;
            overflow-x: auto;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #7e2626d5;
            box-shadow: 2px 5px 10px rgba(0, 0, 0, 0.2);
            padding: 35px;
            min-height: 600px;
            font-size: 15px;
            line-height: 1.6;
        }

        .account-content * {
            font-size: inherit;
        }

        .account-content table {
            width: 100%;
            table-layout: auto;
        }

        @media (max-width: 900px) {
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
            }
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include("../home page/navbar.php"); ?>

    <div class="account-wrapper">

        <div class="account-sidebar">

            <div class="sidebar-user">
                Hello, <?= $_SESSION['delivery_boy_name'] ?? 'Delivery Boy' ?> 👋
            </div>

            <!-- ✅ FIXED -->
            <a href="deliveryboy_profile_main.php" class="<?= $view == 'account' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> My Account
            </a>

            <a href="layout.php?view=dashboard" class="<?= $view == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>

            <a href="layout.php?view=assigned_orders" class="<?= $view == 'assigned_orders' ? 'active' : '' ?>">
                <i class="fa-solid fa-box"></i> Assigned Orders
            </a>

            <a href="layout.php?view=completed_deliveries"
                class="<?= $view == 'completed_deliveries' ? 'active' : '' ?>">
                <i class="fa-solid fa-check-circle"></i> Completed Deliveries
            </a>

            <a href="layout.php?view=profile" class="<?= $view == 'profile' ? 'active' : '' ?>">
                <i class="fa-solid fa-user"></i> My Profile
            </a>

            <!-- ✅ FIXED LOGOUT PATH -->
            <a href="../login/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

        </div>

        <div class="account-content">
            <?php include($page); ?>
        </div>

    </div>

    <?php require_once '../home page/footer.php'; ?>

</body>

</html>
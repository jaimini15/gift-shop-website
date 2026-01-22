<?php
if (!isset($_SESSION))
    session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel | GiftShop</title>

    <!-- MAIN SITE CSS -->
    <link rel="stylesheet" href="../home page/style.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body {
            background: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* =====================
   CONTAINER
===================== */
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 10px;
        }

        /* =====================
   TITLE
===================== */
        .admin-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        /* =====================
   GRID
===================== */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        /* =====================
   CARD
===================== */
        .admin-card {
            border: 1px solid #7e2626d5;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: 0.3s;
            cursor: pointer;
            background: #ffffff;
        }

        .admin-card:hover {
            transform: translateY(-4px);
        }

        /* =====================
   ICONS
===================== */
        .card-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .dashboard {
            color: #2563eb;
        }

        .users {
            color: #16a34a;
        }

        .categories {
            color: #7c3aed;
        }

        .products {
            color: #9333ea;
        }

        .stock {
            color: #0ea5e9;
        }

        .orders {
            color: #f97316;
        }

        .payments {
            color: #22c55e;
        }

        .delivery {
            color: #ef4444;
        }

        .deliveryboys {
            color: #0f766e;
        }

        .feedback {
            color: #64748b;
        }

        .admin-card h3 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .admin-card p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        /* =====================
   RESPONSIVE
===================== */
        @media(max-width:900px) {
            .admin-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:500px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <?php include("../home page/navbar.php"); ?>

    <div class="admin-container">

        <div class="admin-title">
            Hello, Admin 👋
        </div>

        <div class="admin-grid">

            <!-- Dashboard -->
            <div class="admin-card" onclick="location.href='layout.php?view=dashboard'">
                <div class="card-icon dashboard"><i class="fa-solid fa-chart-line"></i></div>
                <h3>Dashboard</h3>
                <p>Admin overview & stats</p>
            </div>

            <div class="admin-card" onclick="">
                <div class="card-icon profile"><i class="fa-solid fa-user"></i></div>
                <h3>My Profile</h3>
                <p>View & edit your profile</p>
            </div>

            <!-- Users -->
            <div class="admin-card" onclick="location.href='layout.php?view=users'">
                <div class="card-icon users"><i class="fa-solid fa-users"></i></div>
                <h3>Users</h3>
                <p>Manage registered users</p>
            </div>

            <!-- Categories -->
            <div class="admin-card" onclick="location.href='layout.php?view=categories'">
                <div class="card-icon categories"><i class="fa-solid fa-layer-group"></i></div>
                <h3>Categories</h3>
                <p>Manage product categories</p>
            </div>

            <!-- Products -->
            <div class="admin-card" onclick="location.href='layout.php?view=products'">
                <div class="card-icon products"><i class="fa-solid fa-box"></i></div>
                <h3>Products</h3>
                <p>Add & manage products</p>
            </div>

            <!-- Stock -->
            <div class="admin-card" onclick="location.href='layout.php?view=stock'">
                <div class="card-icon stock"><i class="fa-solid fa-boxes-packing"></i></div>
                <h3>Stock</h3>
                <p>Inventory management</p>
            </div>

            <!-- Orders -->
            <div class="admin-card" onclick="location.href='layout.php?view=orders'">
                <div class="card-icon orders"><i class="fa-solid fa-cart-shopping"></i></div>
                <h3>Orders</h3>
                <p>Customer orders</p>
            </div>

            <!-- Payments -->
            <div class="admin-card" onclick="location.href='layout.php?view=payments'">
                <div class="card-icon payments"><i class="fa-solid fa-credit-card"></i></div>
                <h3>Payments</h3>
                <p>Transaction details</p>
            </div>

            <!-- Delivery -->
            <div class="admin-card" onclick="location.href='layout.php?view=delivery'">
                <div class="card-icon delivery"><i class="fa-solid fa-truck"></i></div>
                <h3>Delivery</h3>
                <p>Order delivery status</p>
            </div>

            <!-- Delivery Boys -->
            <div class="admin-card" onclick="location.href='layout.php?view=delivery_boys'">
                <div class="card-icon deliveryboys"><i class="fa-solid fa-motorcycle"></i></div>
                <h3>Delivery Boys</h3>
                <p>Manage delivery staff</p>
            </div>

            <!-- Feedback -->
            <div class="admin-card" onclick="location.href='layout.php?view=feedback'">
                <div class="card-icon feedback"><i class="fa-solid fa-comments"></i></div>
                <h3>Feedback</h3>
                <p>User feedback & reviews</p>
            </div>

        </div>

    </div>
<?php require_once '../home page/footer.php'; ?>
</body>

</html>
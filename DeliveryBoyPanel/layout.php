<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Boy Panel | GiftShop</title>

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

        .delivery-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 10px;
        }

        .delivery-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .delivery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .delivery-card {
            border: 1px solid #7e2626d5;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: 0.3s;
            cursor: pointer;
            background: #ffffff;
        }

        .delivery-card:hover {
            transform: translateY(-4px);
        }

        .card-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .dashboard { color: #2563eb; }
        .assigned { color: #f97316; }
        .completed { color: #16a34a; }
        .profile { color: #9333ea; }
        .logout { color: #ef4444; }

        .delivery-card h3 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .delivery-card p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        @media(max-width:900px) {
            .delivery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:500px) {
            .delivery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<?php include("../home page/navbar.php"); ?>

<div class="delivery-container">

    <div class="delivery-title">
        Hello, <?= htmlspecialchars($_SESSION['delivery_boy_name'] ?? 'Delivery Boy') ?> 👋
    </div>

    <div class="delivery-grid">

        <!-- Dashboard -->
        <div class="delivery-card" onclick="location.href='layout.php?view=dashboard'">
            <div class="card-icon dashboard">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3>Dashboard</h3>
            <p>Your delivery overview</p>
        </div>

        <!-- Assigned Orders -->
        <div class="delivery-card" onclick="location.href='layout.php?view=assigned_orders'">
            <div class="card-icon assigned">
                <i class="fa-solid fa-box"></i>
            </div>
            <h3>Assigned Orders</h3>
            <p>Orders waiting for delivery</p>
        </div>

        <!-- Completed Deliveries -->
        <div class="delivery-card" onclick="location.href='layout.php?view=completed_deliveries'">
            <div class="card-icon completed">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <h3>Completed Deliveries</h3>
            <p>Delivered order history</p>
        </div>

        <!-- My Profile -->
        <div class="delivery-card" onclick="location.href='layout.php?view=profile'">
            <div class="card-icon profile">
                <i class="fa-solid fa-user"></i>
            </div>
            <h3>My Profile</h3>
            <p>View & update profile</p>
        </div>

        <!-- Logout -->
        <div class="delivery-card" onclick="location.href='../AdminPanel/logout.php'">
            <div class="card-icon logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3>Logout</h3>
            <p>Sign out safely</p>
        </div>

    </div>

</div>

<?php require_once '../home page/footer.php'; ?>

</body>
</html>

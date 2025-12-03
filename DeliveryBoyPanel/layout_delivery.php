<?php
// Start session (if not started)
if (!isset($_SESSION)) session_start();

// Default page title
if (!isset($page_title)) $page_title = "Admin Panel";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $page_title; ?></title>

<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
/* ===== LAYOUT CSS ===== */

body {
    margin: 0;
    background: #f5f6fa;
    font-family: Arial, sans-serif;
}

.wrapper {
    display: flex;
    height: 100vh;
}

/* ---- SIDEBAR ---- */
.sidebar {
    width: 250px;
    background: #212529;
    color: #fff;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
}

.sidebar .brand {
    padding: 20px;
    background: #0d6efd;
    text-align: center;
    font-size: 22px;
    font-weight: bold;
}

.sidebar .nav-link {
    color: #ddd;
    padding: 12px 20px;
    display: block;
    font-size: 15px;
    border-bottom: 1px solid #2c3034;
}

.sidebar .nav-link:hover {
    background: #0d6efd;
    color: #fff;
}

.sidebar .active {
    background: #0d6efd;
}

/* ---- HEADER ---- */
.header {
    height: 60px;
    background: #fff;
    padding: 10px 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-left: 250px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header h4 {
    margin: 0;
}

/* ---- MAIN CONTENT ---- */
.content {
    margin-left: 250px;
    padding: 20px;
    margin-top: 10px;
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-thumb {
    background: #444;
    border-radius: 10px;
}
</style>

</head>
<body>

<div class="wrapper">

    <!-- ===== LEFT SIDEBAR ===== -->
    <div class="sidebar">
        <div class="brand">Admin Panel</div>
        
        <a href="../dashboard/index.php" class="nav-link">Dashboard</a>

        <!-- Delivery Boy Panel -->
        <a href="../delivery_boy/index.php" class="nav-link">Delivery Dashboard</a>
        <a href="../delivery_boy/delivery_boys.php" class="nav-link">Delivery Boys</a>
        <a href="../delivery_boy/assigned_orders.php" class="nav-link">Assign Orders</a>

        <!-- Add more menu items -->
        <a href="../categories/categories.php" class="nav-link">Categories</a>
        <a href="../products/products.php" class="nav-link">Products</a>
        <a href="../orders/orders.php" class="nav-link">Orders</a>
    </div>

</div>

<!-- ===== TOP HEADER ===== -->
<div class="header">
    <h4><?php echo $page_title; ?></h4>

    <div>
        <span class="me-3">Welcome, Admin</span>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</div>

<!-- ===== PAGE CONTENT AREA ===== -->
<div class="content">
    <!-- 🔽 PAGE CONTENT STARTS HERE -->

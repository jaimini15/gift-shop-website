<?php
if (!isset($_SESSION))
    session_start();
?>

<style>
.report-container {
    max-width:1200px;
    margin:10px auto;
    padding:10px;
}

.report-title{
    font-size:26px;
    font-weight:bold;
    margin-bottom:25px;
}

.report-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.report-card{
    border:1px solid #7e2626d5;
    border-radius:12px;
    padding:25px;
    box-shadow:0 4px 12px rgba(0,0,0,0.03);
    transition:0.3s;
    cursor:pointer;
    background:#ffffff;
}

.report-card:hover{
    transform:translateY(-4px);
}

.card-icon{
    font-size:32px;
    margin-bottom:15px;
}

.sales{color:#2563eb;}
.orders{color:#f97316;}
.payments{color:#22c55e;}
.products{color:#9333ea;}
.users{color:#16a34a;}
.stock{color:#0ea5e9;}

.report-card h3{
    margin:0 0 6px;
    font-size:18px;
}

.report-card p{
    margin:0;
    font-size:14px;
    color:#666;
}
</style>


<div class="report-container">

<div class="report-title">
Reports & Analytics
</div>

<div class="report-grid">


<!-- Orders Report -->
<div class="report-card" onclick="location.href='report/orders_report.php'">
<div class="card-icon orders"><i class="fa-solid fa-cart-shopping"></i></div>
<h3>Orders Report</h3>
<p>Track all customer orders</p>
</div>

<!-- Products Report -->
<div class="report-card" onclick="location.href='report/products_report.php'">
<div class="card-icon products"><i class="fa-solid fa-box"></i></div>
<h3>Products Report</h3>
<p>Product performance</p>
</div>

<!-- Users Report -->
<div class="report-card" onclick="location.href='report/users_report.php'">
<div class="card-icon users"><i class="fa-solid fa-users"></i></div>
<h3>Users Report</h3>
<p>Registered users data</p>
</div>

<!-- Stock Report -->
<div class="report-card" onclick="location.href='report/stock_report.php'">
<div class="card-icon stock"><i class="fa-solid fa-boxes-stacked"></i></div>
<h3>Stock Report</h3>
<p>Inventory report</p>
</div>

</div>

</div>
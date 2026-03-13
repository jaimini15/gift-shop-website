<?php
include("../db.php");

$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

$where = "WHERE 1";

if ($start && $end) {
    $where .= " AND DATE(Order_Date) BETWEEN '$start' AND '$end'";
}

/* ===== CHART DATA ===== */

$query = mysqli_query($connection,"
SELECT 
DATE(Order_Date) as order_date,
COUNT(Order_Id) as total_orders,
SUM(Total_Amount) as revenue
FROM `order`
$where
GROUP BY DATE(Order_Date)
ORDER BY DATE(Order_Date)
");

$labels = [];
$orders = [];

while($row = mysqli_fetch_assoc($query)){
    $labels[] = $row['order_date'];
    $orders[] = $row['total_orders'];
}

/* ===== TABLE DATA ===== */

$orderQuery = mysqli_query($connection,"
SELECT 
Order_Id,
Order_Date,
Total_Amount
FROM `order`
$where
ORDER BY Order_Date DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Order Report</title>

<link rel="stylesheet" href="report.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container">

<h1>Orders Report</h1>

<!-- FILTER -->

<form method="GET" class="filter-box">

<div>
<label>Start Date</label>
<input type="date" name="start" value="<?= $start ?>">
</div>

<div>
<label>End Date</label>
<input type="date" name="end" value="<?= $end ?>">
</div>

<button>Filter</button>

<a href="export_order_pdf.php?start=<?=$start?>&end=<?=$end?>" class="pdf-btn">
Export PDF
</a>

<a href="export_order_excel.php?start=<?=$start?>&end=<?=$end?>" class="excel-btn">
Export Excel
</a>

</form>


<!-- CHART -->

<div class="chart-box">

<canvas id="salesChart"></canvas>

</div>


<!-- TABLE -->

<table>

<tr>
<th>Order ID</th>
<th>Date</th>
<th>Amount</th>
</tr>

<?php while($row=mysqli_fetch_assoc($orderQuery)){ ?>

<tr>

<td><?= $row['Order_Id'] ?></td>

<td><?= $row['Order_Date'] ?></td>

<td>₹<?= number_format($row['Total_Amount'],2) ?></td>

</tr>

<?php } ?>

</table>

</div>


<script>

const labels = <?= json_encode($labels) ?>;
const orders = <?= json_encode($orders) ?>;

const ctx = document.getElementById("salesChart");

const chart = new Chart(ctx,{

type:'bar',

data:{
labels:labels,
datasets:[{
label:'Total Orders',
data:orders
}]
}

});


/* ===== SEND CHART IMAGE TO SERVER FOR PDF ===== */

function saveChart(){

const image = chart.toBase64Image();

fetch("save_chart.php",{
method:"POST",
headers:{
"Content-Type":"application/x-www-form-urlencoded"
},
body:"image="+encodeURIComponent(image)
});

}

saveChart();

</script>

</body>
</html>
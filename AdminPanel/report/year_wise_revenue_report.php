<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

/* ================= YEAR WISE REVENUE ================= */

/* ================= YEAR WISE REVENUE ================= */

$yearQuery = mysqli_query($connection,"
SELECT 
YEAR(Order_Date) AS year,
COUNT(Order_Id) AS total_orders,
SUM(Total_Amount) AS total_revenue
FROM `order`
WHERE Status='CONFIRM'
GROUP BY YEAR(Order_Date)
ORDER BY YEAR(Order_Date)
");

$years = [];
$yearRevenue = [];
$yearOrders = [];

$totalOrders = 0;
$totalRevenue = 0;

while($row=mysqli_fetch_assoc($yearQuery)){

$years[] = $row['year'];
$yearRevenue[] = $row['total_revenue'];
$yearOrders[] = $row['total_orders'];

$totalOrders += $row['total_orders'];
$totalRevenue += $row['total_revenue'];

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Year Wise Revenue Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
font-family:"Segoe UI",Arial;
background:white;
margin:0;
}

.container{
width:94%;
margin:15px auto;
}

/* TITLE */

h1{
color:#7e2626d5;
border-left:5px solid #7e2626d5;
padding-left:8px;
}

/* TITLE ROW */

.title-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}

/* BACK BUTTON */

.back-btn{
text-decoration:none;
font-size:20px;
font-weight:600;
color:#0b6e77;
}

.back-btn:hover{
color:#7e2626d5;
}

/* CHART BOX */

.chart-box{
border:2px solid #7e2626d5;
padding:20px;
width:800px;
height:420px;
margin:auto;
}
/* SUMMARY */

.summary{
display:flex;
gap:12px;
margin-bottom:15px;
}

.summary div{
background:white;
padding:10px 14px;
border-radius:6px;
box-shadow:0 2px 6px rgba(0,0,0,0.06);
font-size:15px;
font-weight:600;
border-left:4px solid #7e2626d5;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
background:white;
border:2px solid #7e2626d5;
margin-top:20px;
}

th{
background:#7e2626d5;
color:white;
padding:8px;
font-size:13px;
border:1px solid #ddd;
}

td{
padding:7px;
font-size:13px;
border:1px solid #ddd;
text-align:center;
}

tr:nth-child(even){
background:#faf7f6;
}

tr:hover{
background:#f2e9e8;
}

tfoot td{
background:#f8eceb;
font-weight:600;
border-top:2px solid #7e2626d5;
}

.pdf-btn,
.excel-btn{
padding:6px 18px;
border-radius:4px;
color:white;
font-weight:600;
font-size:16px;
text-decoration:none;
}

.pdf-btn{
background:#c0392b;
}

.excel-btn{
background:#27ae60;
}

</style>

</head>

<body>

<div class="container">

<div class="title-row">

<h1>Year Wise Revenue Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" class="back-btn">
← Back
</a>

</div>
<!-- SUMMARY -->

<div class="summary">

<div>Total Orders : <?=$totalOrders?></div>

<div>Total Revenue : ₹<?=number_format($totalRevenue,2)?></div>

<a href="export_product_pdf.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>" 
class="pdf-btn">
PDF
</a>
<a href="export_year_revenue_excel.php" class="excel-btn">
Excel
</a>
</div>
<!-- CHART -->

<div class="chart-box">

<canvas id="yearChart"></canvas>

</div>

</div>
<!-- TABLE -->

<table>

<thead>
<tr>
<th>Year</th>
<th>Orders</th>
<th>Revenue</th>
</tr>
</thead>

<tbody>

<?php
for($i=0;$i<count($years);$i++){
?>

<tr>
<td><?=$years[$i]?></td>
<td><?=$yearOrders[$i]?></td>
<td>₹<?=number_format($yearRevenue[$i],2)?></td>
</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>
<td colspan="2" style="text-align:right;">Total Revenue</td>
<td>₹<?=number_format($totalRevenue,2)?></td>
</tr>

</tfoot>

</table>
<script>

const labels = <?=json_encode($years)?>;
const data = <?=json_encode($yearRevenue)?>;

new Chart(document.getElementById("yearChart"),{

type:'bar',

data:{
labels:labels,
datasets:[{
label:'Revenue',
data:data,
backgroundColor:'#7e2626d5',
borderColor:'#7e2626d5',
borderWidth:1,
barThickness:80
}]
},

options:{
responsive:true,
maintainAspectRatio:false,
plugins:{
legend:{display:true}
},
scales:{
y:{
beginAtZero:true
}
}
}

});

</script>

</body>
</html>
<?php
include("../db.php");

$type  = $_GET['type'] ?? '';
$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

$where = "";
$group = "";
$label = "";

/* ===== REPORT TYPE LOGIC ===== */

if($type == "daily"){

$where = "WHERE DATE(Order_Date) = CURDATE()";
$group = "GROUP BY DATE(Order_Date)";
$label = "DATE(Order_Date)";

}
elseif($type == "weekly"){

$where = "WHERE YEARWEEK(Order_Date,1)=YEARWEEK(CURDATE(),1)";
$group = "GROUP BY DAYNAME(Order_Date)";
$label = "DAYNAME(Order_Date)";
$order = "ORDER BY MIN(Order_Date)";

}

elseif($type == "monthly"){

$where = "WHERE MONTH(Order_Date)=MONTH(CURDATE()) AND YEAR(Order_Date)=YEAR(CURDATE())";
$group = "GROUP BY DATE(Order_Date)";
$label = "DATE(Order_Date)";

}

elseif($type == "yearly"){

$where = "WHERE YEAR(Order_Date)=YEAR(CURDATE())";
$group = "GROUP BY MONTHNAME(Order_Date)";
$label = "MONTHNAME(Order_Date)";
$order = "ORDER BY MIN(Order_Date)";

}



/* ===== CUSTOM DATE RANGE ===== */

elseif($start && $end){

$where = "WHERE DATE(Order_Date) BETWEEN '$start' AND '$end'";
$group = "GROUP BY DATE(Order_Date)";
$label = "DATE(Order_Date)";

}

/* ===== CHART QUERY ===== */

$labels = [];
$orders = [];
$revenues = [];
$totalOrders = 0;
$totalRevenue = 0;

if($where!=""){

$order = $order ?? "ORDER BY MIN(Order_Date)";
$query = mysqli_query($connection,"
SELECT 
$label as label,
COUNT(Order_Id) as total_orders,
SUM(Total_Amount) as revenue
FROM `order`
$where
$group
$order
");




while($row=mysqli_fetch_assoc($query)){

$labels[] = $row['label'];
$orders[] = $row['total_orders'];
$revenues[] = $row['revenue'];

$totalOrders += $row['total_orders'];
$totalRevenue += $row['revenue'];

}

}

/* ===== TABLE QUERY ===== */

$tableWhere = "WHERE 1";

if($type=="daily"){
$tableWhere .= " AND DATE(Order_Date)=CURDATE()";
}

elseif($type=="weekly"){
$tableWhere .= " AND YEARWEEK(Order_Date,1)=YEARWEEK(CURDATE(),1)";
}

elseif($type=="monthly"){
$tableWhere .= " AND MONTH(Order_Date)=MONTH(CURDATE()) AND YEAR(Order_Date)=YEAR(CURDATE())";
}

elseif($type=="yearly"){
$tableWhere .= " AND YEAR(Order_Date)=YEAR(CURDATE())";
}

elseif($start && $end){
$tableWhere .= " AND DATE(Order_Date) BETWEEN '$start' AND '$end'";
}

$orderQuery = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
o.Total_Amount,
c.First_Name,
c.Last_Name
FROM `order` o
JOIN user_details c ON o.User_Id = c.User_Id
$tableWhere
ORDER BY o.Order_Date DESC
");


?>

<!DOCTYPE html>
<html>

<head>

<title>Order Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<style>

body{
font-family:"Segoe UI",Arial,sans-serif;
background:white;
margin:0;
color:#333;
}

/* MAIN CONTAINER */

.container{
width:94%;
margin:15px auto;
}

/* TITLE */

h1{
color:#7e2626d5;
margin-bottom:12px;
font-size:24px;
font-weight:600;
border-left:5px solid #7e2626d5;
padding-left:8px;
}

/* FILTER */

.filter-row{
display:flex;
gap:10px;
align-items:flex-end;
flex-wrap:wrap;
background:white;
padding:15px 18px;
border-radius:6px;
box-shadow: 0 2px 15px rgba(0,0,0,0.05); 
margin-bottom:15px;
border:2px solid #7e2626d5;
}

.filter-row label{
font-size:18px;
font-weight:600;
margin-bottom:3px;
}

.filter-row select,
.filter-row input{
padding:6px 8px;
border:1px solid #ddd;
border-radius:4px;
font-size:13px;
min-width:120px;
}

/* BUTTON */

button{
background:#7e2626d5;
color:white;
border:none;
padding:6px 14px;
border-radius:4px;
cursor:pointer;
font-weight:600;
font-size:13px;
transition:0.2s;
}

button:hover{
background:#5f1d1d;
}

/* EXPORT BUTTONS */

.pdf-btn,
.excel-btn{
padding:6px 12px;
border-radius:4px;
color:white;
font-weight:600;
font-size:13px;
text-decoration:none;
}

.pdf-btn{
background:#c0392b;
}

.excel-btn{
background:#27ae60;
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

/* CHART */

.chart-box{
background:white;
padding:10px;
border-radius:6px;
box-shadow:0 2px 6px rgba(0,0,0,0.06);
margin-bottom:18px;
border:2px solid #7e2626d5;
max-width:800px;     /* limit width */
margin-left:auto;
margin-right:auto;
}

/* TITLE ROW */

.title-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:12px;
}

/* BACK BUTTON */

.back-btn{
text-decoration:none;
font-size:20px;
font-weight:600;
color:#0b6e77;
padding:6px 12px;
border-radius:6px;
transition:0.2s;
}

.back-btn:hover{
color:#7e2626d5;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
background:white;
border:2px solid #7e2626d5;
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
</style>

</head>

<body>

<div class="container">

<div class="title-row">

<h1>Orders Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" class="back-btn">
← Back
</a>

</div>



<!-- FILTER -->

<form method="GET">

<div class="filter-row">

<div>
<label>Report Type</label>
<select name="type">
<option value="">Select</option>
<option value="daily" <?=($type=="daily")?"selected":""?>>Daily</option>
<option value="weekly" <?=($type=="weekly")?"selected":""?>>Weekly</option>
<option value="monthly" <?=($type=="monthly")?"selected":""?>>Monthly</option>
<option value="yearly" <?=($type=="yearly")?"selected":""?>>Yearly</option>
</select>
</div>

<div>
<label>Start Date</label>
<input type="date" name="start" value="<?=$start?>">
</div>

<div>
<label>End Date</label>
<input type="date" name="end" value="<?=$end?>">
</div>

<button type="submit">Filter</button>

<a href="#" class="pdf-btn" onclick="generatePDF()">PDF</a>

<a href="export_order_excel.php?type=<?=$type?>&start=<?=$start?>&end=<?=$end?>" class="excel-btn">
Excel
</a>

</div>

</form>


<!-- TOTALS -->

<div class="summary">

<div>Total Orders : <?=$totalOrders?></div>

<div>Total Revenue : ₹<?=number_format($totalRevenue,2)?></div>

</div>


<!-- CHART -->

<div class="chart-box">

<canvas id="salesChart"></canvas>

</div>


<!-- TABLE -->

<table>

<thead>
<tr>
<th>Order ID</th>
<th>Customer Name</th>
<th>Date</th>
<th>Amount</th>
</tr>
</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($orderQuery)){ ?>

<tr>
<td><?=$row['Order_Id']?></td>
<td><?=$row['First_Name']?> <?=$row['Last_Name']?></td>
<td><?=date("d/m/Y", strtotime($row['Order_Date']))?></td>
<td>₹<?=number_format($row['Total_Amount'],2)?></td>
</tr>

<?php } ?>

</tbody>

<tfoot>

<tr style="background:#f2e9e8;font-weight:600;">
<td colspan="3" style="text-align:right;">Total Revenue</td>
<td>₹<?=number_format($totalRevenue,2)?></td>
</tr>

</tfoot>

</table>

</div>

<script>
function generatePDF(){

setTimeout(function(){

window.open("export_order_pdf.php?type=<?=$type?>&start=<?=$start?>&end=<?=$end?>","_blank");

},3000);

}</script>
<script>

const labels = <?=json_encode($labels)?>;
const orders = <?=json_encode($orders)?>;
const revenues = <?=json_encode($revenues)?>;
const maxOrders = Math.max(...orders);
const yAxisMax = maxOrders + 1;
Chart.register(ChartDataLabels);

const ctx = document.getElementById("salesChart");

new Chart(ctx,{
type:'bar',
data:{
labels:labels,
datasets:[{
label:'Orders',
data:orders,   // bar height = number of orders
backgroundColor:'#7e2626d5',
borderColor:'#7e2626d5',
borderWidth:1,
barThickness:80
}]
},
options:{
responsive:true,
plugins:{

title:{
display:true,
text:'Orders & Revenue Report',
font:{size:18}
},

legend:{
display:true
},

/* BAR LABEL = REVENUE */

datalabels:{
color:'#000',
anchor:'end',
align:'top',
font:{
weight:'bold',
size:12
},
formatter:function(value,context){

let revenue = revenues[context.dataIndex];

return "₹"+revenue;

}
},

/* HOVER TOOLTIP */

tooltip:{
callbacks:{
label:function(context){

let index = context.dataIndex;

let order = orders[index];
let revenue = revenues[index];

return [
"Orders : "+order,
"Revenue : ₹"+revenue
];

}
}
}

},

scales:{
y: {
    beginAtZero: true,
    max: yAxisMax,
    ticks: {
        stepSize: 1
    },
    title:{
        display:true,
        text:"Number of Orders"
    }
},
x:{
title:{
display:true,
text:'Time Period'
}
}
}

}

});

/* SAVE CHART FOR PDF */

setTimeout(function(){

const canvas = document.getElementById("salesChart");

const image = canvas.toDataURL("image/png");

fetch("save_chart.php",{
method:"POST",
headers:{
"Content-Type":"application/json"
},
body: JSON.stringify({image:image})
});

},3000);

</script>

</body>
</html>

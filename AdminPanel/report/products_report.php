<?php
if (!isset($_SESSION))
    session_start();

include(__DIR__ . '/../db.php');

/* ================= PRODUCT LIST ================= */

$productListQuery = mysqli_query($connection,"
SELECT Product_Id, Product_Name
FROM product_details
ORDER BY Product_Name
");

$productFilter = $_GET['product_id'] ?? '';
$periodFilter = $_GET['period'] ?? '';

$productLabels = [];
$productSales = [];
$productOrders = [];

$totalOrders = 0;
$totalRevenue = 0;

/* ================= REPORT LOGIC ================= */

if($productFilter && $periodFilter){

/* ===== DAILY ===== */
if($periodFilter=="daily"){

$query="
SELECT 
DATE(o.Order_Date) AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
COUNT(DISTINCT o.Order_Id) AS orders
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE oi.Product_Id='$productFilter'
AND DATE(o.Order_Date)=CURDATE()
GROUP BY DATE(o.Order_Date)
";

}

/* ===== WEEKLY ===== */
elseif($periodFilter=="weekly"){

$query="
SELECT 
DAYNAME(o.Order_Date) AS label,
WEEKDAY(o.Order_Date) AS day_no,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
COUNT(DISTINCT o.Order_Id) AS orders
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE oi.Product_Id='$productFilter'
AND YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)
GROUP BY label, day_no
ORDER BY day_no
";

}

/* ===== MONTHLY ===== */
elseif($periodFilter=="monthly"){

$query="
SELECT 
DATE(o.Order_Date) AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
COUNT(DISTINCT o.Order_Id) AS orders
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE oi.Product_Id='$productFilter'
AND MONTH(o.Order_Date)=MONTH(CURDATE())
AND YEAR(o.Order_Date)=YEAR(CURDATE())
GROUP BY DATE(o.Order_Date)
";

}

/* ===== YEARLY ===== */
else{

$query="
SELECT 
DATE_FORMAT(o.Order_Date,'%M') AS label,
MONTH(o.Order_Date) AS month_no,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
COUNT(DISTINCT o.Order_Id) AS orders
FROM order_item oi
JOIN `order` o ON oi.Order_Id = o.Order_Id
WHERE oi.Product_Id = '$productFilter'
AND YEAR(o.Order_Date) = YEAR(CURDATE())
GROUP BY month_no, label
ORDER BY month_no
";

}

/* RUN QUERY */
$result = mysqli_query($connection,$query);

/* PROCESS DATA */
if($result && mysqli_num_rows($result)>0){

if($periodFilter=="weekly"){

$weekDays=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

$tempRevenue=[];
$tempOrders=[];

while($row=mysqli_fetch_assoc($result)){

$tempRevenue[$row['label']]=$row['revenue'];
$tempOrders[$row['label']]=$row['orders'];

$totalOrders += $row['orders'];
$totalRevenue += $row['revenue'];

}

foreach($weekDays as $day){

$productLabels[]=$day;
$productSales[]=$tempRevenue[$day] ?? 0;
$productOrders[]=$tempOrders[$day] ?? 0;

}

}else{

while($row=mysqli_fetch_assoc($result)){

$productLabels[]=$row['label'];
$productSales[]=$row['revenue'];
$productOrders[]=$row['orders'];

$totalOrders += $row['orders'];
$totalRevenue += $row['revenue'];

}

}

}

/* IF NO DATA */
if(empty($productLabels)){

$productLabels[]="No Orders Yet";
$productSales[]=0;
$productOrders[]=0;

}

}


$productName = "";

if($productFilter){
    $nameQuery = mysqli_query($connection,"SELECT Product_Name FROM product_details WHERE Product_Id='$productFilter'");
    if($row=mysqli_fetch_assoc($nameQuery)){
        $productName = $row['Product_Name'];
    }
}
$periodCondition = "";

if($periodFilter == "daily"){
    $periodCondition = "AND DATE(o.Order_Date) = CURDATE()";
}
elseif($periodFilter == "weekly"){
    $periodCondition = "AND YEARWEEK(o.Order_Date,1) = YEARWEEK(CURDATE(),1)";
}
elseif($periodFilter == "monthly"){
    $periodCondition = "AND MONTH(o.Order_Date) = MONTH(CURDATE()) 
                        AND YEAR(o.Order_Date) = YEAR(CURDATE())";
}
elseif($periodFilter == "yearly"){
    $periodCondition = "AND YEAR(o.Order_Date) = YEAR(CURDATE())";
}

$orderDetails = [];

if($productFilter && $periodFilter){

$detailQuery = mysqli_query($connection,"
SELECT 
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer_name,
o.Order_Date,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN `order` o ON oi.Order_Id = o.Order_Id
JOIN user_details u ON o.User_Id = u.User_Id
WHERE oi.Product_Id='$productFilter'
$periodCondition
GROUP BY o.Order_Id
ORDER BY o.Order_Date DESC
");

while($row=mysqli_fetch_assoc($detailQuery)){
$orderDetails[] = $row;
}

}


?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Product Sales Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<style>

body{
font-family:"Segoe UI",Arial,sans-serif;
background:white;
margin:0;
color:#333;
}

/* CONTAINER */

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
box-shadow:0 2px 15px rgba(0,0,0,0.05);
margin-bottom:15px;
border:2px solid #7e2626d5;
}

.filter-row label{
font-size:18px;
font-weight:600;
margin-bottom:3px;
}

.filter-row select{
padding:6px 8px;
border:1px solid #ddd;
border-radius:4px;
font-size:13px;
min-width:140px;
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
}

button:hover{
background:#5f1d1d;
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
max-width:800px;
margin-left:auto;
margin-right:auto;
}
.chart-box{
height:320px;
}

/* PDF  and excel*/
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
.excel-btn{
background:#27ae60;
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
table th[colspan]{
background:#f7eaea;
color:black;
font-size:16px;
text-align:left;
padding:10px;
}

/* Back Button */
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
tfoot td{
font-size:14px;
}


</style>

</head>

<body>

<div class="container">

<div class="title-row">
<h1>Product Sales Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" class="back-btn">
← Back
</a>

</div>


<!-- FILTER -->

<form method="GET">

<div class="filter-row">

<div>

<label>Select Product</label>

<select name="product_id">

<option value="">Select Product</option>

<?php while($p=mysqli_fetch_assoc($productListQuery)){ ?>

<option value="<?=$p['Product_Id']?>" <?=($productFilter==$p['Product_Id'])?'selected':''?>>

<?=$p['Product_Name']?>

</option>

<?php } ?>

</select>

</div>

<div>

<label>Select Period</label>

<select name="period">

<option value="">Select Period</option>

<option value="daily" <?=($periodFilter=='daily')?'selected':''?>>Daily</option>

<option value="weekly" <?=($periodFilter=='weekly')?'selected':''?>>Weekly</option>

<option value="monthly" <?=($periodFilter=='monthly')?'selected':''?>>Monthly</option>

<option value="yearly" <?=($periodFilter=='yearly')?'selected':''?>>Yearly</option>

</select>

</div>

<button type="submit">Filter</button>
<a href="export_product_pdf.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>" 
class="pdf-btn">
PDF
</a>
<a href="export_product_excel.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>"
class="excel-btn">
Excel
</a>

</div>

</form>

<!-- SUMMARY -->

<div class="summary">

<div>Total Orders : <?=$totalOrders?></div>

<div>Total Revenue : ₹<?=number_format($totalRevenue,2)?></div>

</div>

<!-- CHART -->

<div class="chart-box">

<canvas id="productSalesChart"></canvas>

</div>

<!-- TABLE -->

<table>

<tr>
<th colspan="4" style="text-align:left;font-size:15px;">
Product : <?=$productName?>
</th>
</tr>

<tr>
<th>Order ID</th>
<th>Customer Name</th>
<th>Date of Order</th>
<th>Revenue</th>
</tr>

<?php if(!empty($orderDetails)){ ?>

<?php foreach($orderDetails as $order){ ?>

<tr>

<td><?=$order['Order_Id']?></td>

<td><?=$order['customer_name']?></td>

<td><?=date("d M Y",strtotime($order['Order_Date']))?></td>

<td>₹<?=number_format($order['revenue'],2)?></td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>
<td colspan="4">No Orders Found</td>
</tr>

<?php } ?>
<tfoot>

<tr>

<td colspan="3" style="text-align:right;font-weight:600;background:#f7eaea;">
Total Revenue
</td>

<td style="font-weight:700;background:#f7eaea;">
₹<?=number_format($totalRevenue,2)?>
</td>

</tr>

</tfoot>

</table>

</div>
<script>
const labels = <?=json_encode($productLabels)?>;
const revenue = <?=json_encode($productSales)?>;
const orders = <?=json_encode($productOrders)?>;
const maxOrders = Math.max(...orders);
const yAxisMax = maxOrders + 1;
const ctx = document.getElementById("productSalesChart");

new Chart(ctx, {

type: 'bar',

data: {
    labels: labels,
    datasets: [{
        label: 'Orders',
        data: orders,
        backgroundColor: '#7e2626d5',
        borderColor: '#7e2626d5',
        borderWidth: 1,
        barThickness: 50
    }]
},

plugins: [ChartDataLabels],

options: {

responsive:true,
maintainAspectRatio:false,
layout:{
padding:{ top:20 }
},
plugins: {

legend:{display:false},

tooltip:{
callbacks:{
label:function(context){
return [
"Orders: "+context.raw,
"Revenue: ₹"+revenue[context.dataIndex]
];
}
}
},
datalabels:{
    anchor:'end',
    align:'top',

    color:'#000',

    font:{
        weight:'bold',
        size:12
    },

    formatter:function(value,context){
        return "₹"+revenue[context.dataIndex];
    }
}
},

scales:{

y:{
beginAtZero:true,
max:yAxisMax,
ticks:{
stepSize:1
},
title:{
display:true,
text:"Number of Orders"
}
}

}

}

});
</script>

</body>

</html>

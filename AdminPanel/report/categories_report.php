<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

$periodFilter = $_GET['period'] ?? 'yearly';
$monthFilter = $_GET['month'] ?? '';

$labels=[];
$data=[];
$tableData=[];
$totalRevenue=0;

/* ================= DEFAULT REPORT ================= */
/* TOTAL CATEGORY REVENUE */

if(!$periodFilter){

$query="
SELECT 
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
DATE(o.Order_Date) AS order_date,
p.Product_Name,
c.Category_Name,
(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN product_details p ON oi.Product_Id=p.Product_Id
JOIN category_details c ON p.Category_Id=c.Category_Id
JOIN `order` o ON oi.Order_Id=o.Order_Id
JOIN user_details u ON o.User_Id=u.User_Id
ORDER BY c.Category_Name,o.Order_Date DESC
";


}

/* ================= DAILY ================= */

elseif($periodFilter=="daily"){

$query="
SELECT 
c.Category_Name AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN product_details p ON oi.Product_Id=p.Product_Id
JOIN category_details c ON p.Category_Id=c.Category_Id
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE DATE(o.Order_Date)=CURDATE()
GROUP BY c.Category_Id
";

}

/* ================= WEEKLY ================= */

elseif($periodFilter=="weekly"){

$query="
SELECT 
c.Category_Name AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN product_details p ON oi.Product_Id=p.Product_Id
JOIN category_details c ON p.Category_Id=c.Category_Id
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)
GROUP BY c.Category_Id
";

}

/* ================= MONTHLY ================= */

elseif($periodFilter=="monthly"){

$query="
SELECT 
c.Category_Name AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN product_details p ON oi.Product_Id=p.Product_Id
JOIN category_details c ON p.Category_Id=c.Category_Id
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE MONTH(o.Order_Date)=MONTH(CURDATE())
AND YEAR(o.Order_Date)=YEAR(CURDATE())
GROUP BY c.Category_Id
";

}

/* ================= YEARLY ================= */

elseif($periodFilter=="yearly"){

/* YEARLY + MONTH FILTER */

if($monthFilter){

$query="
SELECT 
c.Category_Name AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN product_details p ON oi.Product_Id=p.Product_Id
JOIN category_details c ON p.Category_Id=c.Category_Id
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$monthFilter'
GROUP BY c.Category_Id
";

}

/* YEARLY WITHOUT MONTH */

else{

$query="
SELECT 
DATE_FORMAT(o.Order_Date,'%M') AS label,
MONTH(o.Order_Date) AS month_no,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
GROUP BY month_no,label
ORDER BY month_no
";

}

}
/* ================= TABLE DATA ================= */

$whereCondition="";

/* DAILY */
if($periodFilter=="daily"){
$whereCondition="WHERE DATE(o.Order_Date)=CURDATE()";
}

/* WEEKLY */
elseif($periodFilter=="weekly"){
$whereCondition="WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)";
}

/* MONTHLY */
elseif($periodFilter=="monthly"){
$whereCondition="WHERE MONTH(o.Order_Date)=MONTH(CURDATE()) 
AND YEAR(o.Order_Date)=YEAR(CURDATE())";
}

/* YEARLY */
elseif($periodFilter=="yearly"){

if($monthFilter){

$whereCondition="WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$monthFilter'";

}else{

$whereCondition="WHERE YEAR(o.Order_Date)=YEAR(CURDATE())";

}

}

/* TABLE QUERY */

$tableQuery="

SELECT 
c.Category_Name,
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
DATE(o.Order_Date) AS order_date,
p.Product_Name,
(oi.Quantity * oi.Price_Snapshot) AS revenue

FROM order_item oi
JOIN product_details p ON oi.Product_Id = p.Product_Id
JOIN category_details c ON p.Category_Id = c.Category_Id
JOIN `order` o ON oi.Order_Id = o.Order_Id
JOIN user_details u ON o.User_Id = u.User_Id

$whereCondition

ORDER BY c.Category_Name , o.Order_Date DESC

";

$tableResult = mysqli_query($connection,$tableQuery);

$tableData=[];
$totalRevenue=0;

while($row=mysqli_fetch_assoc($tableResult)){

$tableData[]=$row;
$totalRevenue += $row['revenue'];

}
/* ================= EXECUTE CHART QUERY ================= */

$result = mysqli_query($connection,$query);

while($row = mysqli_fetch_assoc($result)){

/* handle both cases (monthly/yearly labels) */

if(isset($row['label'])){
$labels[] = $row['label'];
}

if(isset($row['revenue'])){
$data[] = $row['revenue'];
}

}

if(empty($labels)){
$labels[]="No Data";
$data[]=0;
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<title>Category Revenue Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

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
    max-width:800px;
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

.category-header{
    background:#f8f3ee;
    font-weight:600;
    color:#7e2626d5;
}
</style>


</head>

<body>

<div class="container">

<div class="title-row">

<h1>Category Revenue Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" 
class="back-btn">
← Back
</a>

</div>


<form method="GET">

<div class="filter-row">

<label>Period</label>

<select name="period">

<option value="">All</option>
<option value="daily" <?=($periodFilter=='daily')?'selected':''?>>Daily</option>
<option value="weekly" <?=($periodFilter=='weekly')?'selected':''?>>Weekly</option>
<option value="monthly" <?=($periodFilter=='monthly')?'selected':''?>>Monthly</option>
<option value="yearly" <?=($periodFilter=='yearly')?'selected':''?>>Yearly</option>


</select>

<?php if($periodFilter=="yearly"){ ?>

<label>Month</label>

<select name="month">

<option value="">Select</option>

<?php
for($m=1;$m<=12;$m++){
$monthName=date("F", mktime(0,0,0,$m,10));
?>

<option value="<?=$m?>" <?=($monthFilter==$m)?'selected':''?>>
<?=$monthName?>
</option>

<?php } ?>

</select>

<?php } ?>

<button type="submit">Filter</button>

</form> <!-- CLOSE GET FORM -->

<form method="POST" action="export_category_pdf.php?period=<?=$periodFilter?>&month=<?=$monthFilter?>" target="_blank">

<input type="hidden" name="chart_image" id="chartImage">

<button type="submit" class="pdf-btn" onclick="saveChart()">PDF</button>

</form>


<a href="export_category_revenue_excel.php?period=<?=$periodFilter?>&month=<?=$monthFilter?>" class="excel-btn">
Excel
</a>
</div>

</form>

<div class="summary">

<div>Total Revenue : ₹<?=number_format($totalRevenue,2)?></div>
<div>Total Categories : <?=count($labels)?></div>

</div>

<div class="chart-box">

<canvas id="categoryChart"></canvas>

</div>

<!-- TABLE -->

<table>

<thead>

<tr>
<th>Order ID</th>
<th>Customer Name</th>
<th>Date of Order</th>
<th>Product Name</th>
<th>Revenue</th>
</tr>

</thead>

<tbody>

<?php

$currentCategory="";

foreach($tableData as $row){
    if(!isset($row['Category_Name'])) continue;

if($currentCategory != $row['Category_Name']){

$currentCategory = $row['Category_Name'];

?>

<tr style="background:#f8f3ee;font-weight:bold;color:#7e2626d5;">
<td colspan="5">
Category : <?=$currentCategory?>
</td>
</tr>

<?php } ?>

<tr>

<td><?=$row['Order_Id']?></td>

<td><?=$row['customer']?></td>

<td><?=$row['order_date']?></td>

<td><?=$row['Product_Name']?></td>

<td>₹<?=number_format($row['revenue'],2)?></td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr style="font-weight:bold;background:#f1f1f1">

<td colspan="4" style="text-align:right">
Total Revenue
</td>

<td>
₹<?=number_format($totalRevenue,2)?>
</td>

</tr>

</tfoot>

</table>

</div>

<script>

const labels = <?=json_encode($labels)?>;
const revenue = <?=json_encode($data)?>;
const total = revenue.reduce((a,b)=> Number(a) + Number(b), 0);


new Chart(document.getElementById("categoryChart"),{

type:'pie',

data:{
labels:labels,
datasets:[{
data:revenue,
backgroundColor:[
'#7e2626d5',
'#a94442',
'#c97d60',
'#d4a373',
'#e6ccb2',
'#bc4749'
]
}]
},

plugins:[ChartDataLabels],

options:{
responsive:true,
maintainAspectRatio:false,

plugins:{

legend:{
position:'right'
},

datalabels:{
color:'white',
font:{weight:'bold',size:12},

formatter:(value,ctx)=>{

value = Number(value);

if(!total || total === 0){
return "₹0";
}

let percentage = ((value / total) * 100).toFixed(1);

if(isNaN(percentage)){
percentage = 0;
}

return percentage + "%\n₹" + value.toLocaleString();

}

}

}

}

});
function saveChart(){

const chart = document.getElementById("categoryChart");

const image = chart.toDataURL("image/png");

document.getElementById("chartImage").value=image;

}

</script>

</body>
</html>

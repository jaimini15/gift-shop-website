<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

/* STATUS FILTER */
$statusFilter = $_GET['status'] ?? '';

$labels = [];
$data = [];
$tableData = [];
$totalOrders = 0;

/* ================= CHART DATA ================= */

$chartWhere = "";

if($statusFilter == "CONFIRM"){
    $chartWhere = "WHERE d.Delivery_Status IS NULL";
}
elseif($statusFilter != ""){
    $chartWhere = "WHERE d.Delivery_Status='$statusFilter'";
}

$chartQuery="

SELECT status, COUNT(*) total
FROM(

SELECT

CASE
WHEN d.Delivery_Status='Delivered' THEN 'Delivered'
WHEN d.Delivery_Status='Out for Delivery' THEN 'Out for Delivery'
WHEN d.Delivery_Status='Packed' THEN 'Packed'
ELSE 'CONFIRM'
END AS status

FROM `order` o
LEFT JOIN delivery_details d
ON o.Order_Id=d.Order_Id

$chartWhere

) t

GROUP BY status

";

$result=mysqli_query($connection,$chartQuery);

while($row=mysqli_fetch_assoc($result)){

$labels[]=$row['status'];
$data[]=$row['total'];

}

/* ================= TABLE FILTER ================= */

$whereCondition="";

if($statusFilter != ""){

if($statusFilter=="CONFIRM"){
$whereCondition = "WHERE d.Delivery_Status IS NULL";
}
else{
$whereCondition = "WHERE d.Delivery_Status='$statusFilter'";
}

}

/* ================= TABLE QUERY ================= */

$tableQuery="

SELECT 
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
d.Delivery_Address,
a.Area_Name,
DATE(o.Order_Date) AS order_date,
o.Total_Amount,

CASE
WHEN d.Delivery_Status='Delivered' THEN 'Delivered'
WHEN d.Delivery_Status='Out for Delivery' THEN 'Out for Delivery'
WHEN d.Delivery_Status='Packed' THEN 'Packed'
ELSE 'CONFIRM'
END AS status

FROM `order` o

LEFT JOIN delivery_details d 
ON o.Order_Id=d.Order_Id

LEFT JOIN user_details u
ON o.User_Id=u.User_Id

LEFT JOIN area_details a
ON d.Area_Id=a.Area_Id

$whereCondition

ORDER BY o.Order_Date DESC

";

$tableResult = mysqli_query($connection,$tableQuery);

while($row=mysqli_fetch_assoc($tableResult)){

$tableData[] = $row;
$totalOrders++;

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<title>Delivery Status Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<style>

body{
font-family:"Segoe UI",Arial;
margin:0;
background:white;
}

.container{
width:94%;
margin:15px auto;
}

h1{
color:#7e2626d5;
border-left:5px solid #7e2626d5;
padding-left:8px;
}

.filter-row{
display:flex;
gap:10px;
align-items:end;
border:2px solid #7e2626d5;
padding:12px;
margin-bottom:15px;
}

select{
padding:6px;
}

button{
background:#7e2626d5;
color:white;
border:none;
padding:6px 12px;
cursor:pointer;
}

.summary{
display:flex;
gap:15px;
margin-bottom:15px;
}

.summary div{
border-left:4px solid #7e2626d5;
padding:8px 12px;
font-weight:600;
}

.chart-box{
border:2px solid #7e2626d5;
padding:10px;
width:500px;
height:350px;
margin:auto;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
border:1px solid #ddd;
padding:8px;
text-align:center;
}

th{
background:#7e2626d5;
color:white;
}

.title-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:12px;
}

.back-btn{
text-decoration:none;
font-size:17px;
font-weight:600;
color:#0b6e77;
}

.back-btn:hover{
color:#7e2626d5;
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

</style>

</head>

<body>

<div class="container">

<div class="title-row">

<h1>Delivery Status Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" class="back-btn">
← Back
</a>

</div>

<form method="GET">

<div class="filter-row">

<label>Status</label>

<select name="status">

<option value="">All Orders</option>

<option value="CONFIRM" <?=($statusFilter=='CONFIRM')?'selected':''?>>Confirm</option>

<option value="Packed" <?=($statusFilter=='Packed')?'selected':''?>>Packed</option>

<option value="Out for Delivery" <?=($statusFilter=='Out for Delivery')?'selected':''?>>Out for Delivery</option>

<option value="Delivered" <?=($statusFilter=='Delivered')?'selected':''?>>Delivered</option>

</select>

<button type="submit">Filter</button>
<a href="export_delivery_status_pdf.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>" 
class="pdf-btn">
PDF
</a>
<a href="export_delivery_status_excel.php?status=<?=$statusFilter?>" 
class="excel-btn">
Excel
</a>
</div>

</form>

<div class="summary">

<div>Total Orders : <?=$totalOrders?></div>
<div>Total Status Types : 4</div>

</div>

<div class="chart-box">

<canvas id="statusChart"></canvas>

</div>

<table>

<thead>

<tr>
<th>Order ID</th>
<th>Customer Name</th>
<th>Address</th>
<th>Area</th>
<th>Order Date</th>
<th>Amount</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php foreach($tableData as $row){ ?>

<tr>

<td><?=$row['Order_Id']?></td>
<td><?=$row['customer']?></td>
<td><?=$row['Delivery_Address']?></td>
<td><?=$row['Area_Name']?></td>
<td><?=$row['order_date']?></td>
<td>₹<?=number_format($row['Total_Amount'],2)?></td>
<td><?=$row['status']?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<script>

const labels = <?=json_encode($labels)?>;
const data = <?=json_encode($data)?>;

new Chart(document.getElementById("statusChart"),{

type:'pie',

data:{
labels:labels,
datasets:[{
data:data,
backgroundColor:[
'#7e2626d5',
'#a94442',
'#c97d60',
'#d4a373'
]
}]
},

plugins:[ChartDataLabels],

options:{
responsive:true,
maintainAspectRatio:false,

plugins:{
legend:{ position:'right' },

datalabels:{
color:'white',
font:{weight:'bold'},
formatter:(value)=> value
}

}

}

});

</script>

</body>
</html>
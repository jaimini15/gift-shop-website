<?php
if (!isset($_SESSION)) {
    session_start();
}

include(__DIR__ . '/../db.php');

/* ================= STATUS FILTER ================= */

$statusFilter = $_GET['status'] ?? '';

$labels = [];
$data = [];
$tableData = [];
$totalOrders = 0;


/* ================= CHART QUERY ================= */

$chartWhere = "";

if ($statusFilter == "CONFIRM") {
    $chartWhere = "WHERE d.Delivery_Status IS NULL";
} 
elseif ($statusFilter != "") {
    $chartWhere = "WHERE d.Delivery_Status='$statusFilter'";
}

$chartQuery = "

SELECT status, COUNT(*) total
FROM (

SELECT
CASE
WHEN d.Delivery_Status='Delivered' THEN 'Delivered'
WHEN d.Delivery_Status='Out for Delivery' THEN 'Out for Delivery'
WHEN d.Delivery_Status='Packed' THEN 'Packed'
ELSE 'CONFIRM'
END AS status

FROM `order` o
LEFT JOIN delivery_details d
ON o.Order_Id = d.Order_Id

$chartWhere

) t

GROUP BY status

";

$result = mysqli_query($connection,$chartQuery);

while($row=mysqli_fetch_assoc($result)){

$labels[] = $row['status'];
$data[]   = (int)$row['total'];

}

/* HANDLE EMPTY CHART */

if(empty($labels)){
$labels[]="No Orders";
$data[]=0;
}


/* ================= TABLE FILTER ================= */

$whereCondition="";

if($statusFilter!=""){

if($statusFilter=="CONFIRM"){
$whereCondition="WHERE d.Delivery_Status IS NULL";
}
else{
$whereCondition="WHERE d.Delivery_Status='$statusFilter'";
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

$tableResult=mysqli_query($connection,$tableQuery);

while($row=mysqli_fetch_assoc($tableResult)){

$tableData[]=$row;
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
font-family:"Segoe UI",Arial,sans-serif;
background:white;
margin:0;
color:#333;
}

.container{
width:94%;
margin:15px auto;
}

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
}

.filter-row select{
padding:6px 8px;
border:1px solid #ddd;
border-radius:4px;
font-size:13px;
min-width:120px;
}

/* BUTTONS */

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

/* EXPORT BUTTONS */

.pdf-btn{
background:#c0392b;
}

.excel-btn{
background:#27ae60;
padding:6px 12px;
border-radius:4px;
color:white;
font-weight:600;
font-size:13px;
text-decoration:none;
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
height:350px;
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

</style>

</head>

<body>

<div class="container">

<h1>Delivery Status Report</h1>

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

</form>

<!-- PDF EXPORT -->

<form method="POST"
action="export_delivery_status_pdf.php?status=<?=$statusFilter?>"
target="_blank">

<input type="hidden" name="chart_image" id="chartImage">

<button type="submit" class="pdf-btn" onclick="saveChart()">
PDF
</button>

</form>

<a href="export_delivery_status_excel.php?status=<?=$statusFilter?>" class="excel-btn">
Excel
</a>

</div>


<div class="summary">

<div>Total Orders : <?=$totalOrders?></div>
<div>Total Status Types : <?=count($labels)?></div>

</div>


<div class="chart-box">
<canvas id="statusChart"></canvas>
</div>


<table>

<thead>

<tr>
<th>Order ID</th>
<th>Customer</th>
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
<td><?=$row['Delivery_Address'] ?? '-'?></td>
<td><?=$row['Area_Name'] ?? '-'?></td>
<td><?=$row['order_date']?></td>
<td>₹<?=number_format($row['Total_Amount'],2)?></td>
<td><?=$row['status']?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>


<script>

/* CHART DATA */

const labels = <?=json_encode($labels)?>;
const data   = <?=json_encode($data)?>;

const total = data.reduce((a,b)=>a+b,0);


/* CREATE CHART */

const chart = new Chart(document.getElementById("statusChart"),{

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

formatter:(value)=>{

if(!total) return "0%";

let percent=((value/total)*100).toFixed(1);
return percent+"%";

}

}

}

}

});


/* SAVE CHART FOR PDF */

function saveChart(){

const canvas = document.getElementById("statusChart");
const image = canvas.toDataURL("image/png");

document.getElementById("chartImage").value = image;

}

</script>

</body>
</html>
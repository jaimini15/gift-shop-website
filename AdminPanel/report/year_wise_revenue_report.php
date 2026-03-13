<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

/* ================= YEAR WISE REVENUE ================= */

$yearQuery = mysqli_query($connection,"
SELECT 
YEAR(Order_Date) AS year,
SUM(Total_Amount) AS total_revenue
FROM `order`
WHERE Status='CONFIRM'
GROUP BY YEAR(Order_Date)
ORDER BY YEAR(Order_Date)
");

$years = [];
$yearRevenue = [];

while($row=mysqli_fetch_assoc($yearQuery)){

$years[] = $row['year'];
$yearRevenue[] = $row['total_revenue'];

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
font-size:17px;
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

<!-- CHART -->

<div class="chart-box">

<canvas id="yearChart"></canvas>

</div>

</div>

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
<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

$type  = $_GET['type'] ?? '';
$limit = $_GET['limit'] ?? '';

/* ================= QUERY ================= */

$query = "
SELECT 
c.Category_Name,
p.Product_Id,
p.Product_Name,
SUM(oi.Quantity) AS Total_Sold

FROM order_item oi

JOIN product_details p 
ON oi.Product_Id = p.Product_Id

JOIN category_details c 
ON p.Category_Id = c.Category_Id

GROUP BY p.Product_Id
";

/* FILTER */

if($type=="top" && $limit){
$query .= " ORDER BY Total_Sold DESC LIMIT $limit";
}
elseif($type=="low" && $limit){
$query .= " ORDER BY Total_Sold ASC LIMIT $limit";
}
else{
$query .= " ORDER BY c.Category_Name, p.Product_Name";
}

$result = mysqli_query($connection,$query);

$data=[];

while($row=mysqli_fetch_assoc($result)){
$data[]=$row;
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Top / Least Selling Products Report</title>

<style>

body{
font-family:Segoe UI;
background:white;
margin:0;
}

.container{
width:95%;
margin:auto;
margin-top:20px;
}

h1{
color:#7e2626d5;
border-left:5px solid #7e2626d5;
padding-left:8px;
}

.filter{
border:2px solid #7e2626d5;
padding:10px;
margin-bottom:20px;
display:flex;
gap:10px;
align-items:end;
}

select,button{
padding:6px;
}

button{
background:#7e2626d5;
color:white;
border:none;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#7e2626d5;
color:white;
padding:8px;
border:1px solid #ddd;
}

td{
padding:8px;
border:1px solid #ddd;
text-align:center;
}

.category-header{
background:#f8f3ee;
font-weight:bold;
color:#7e2626d5;
}

tfoot td{
font-weight:bold;
background:#f1f1f1;
}

.title-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}

.back-btn{
text-decoration:none;
font-size:16px;
font-weight:600;
color:#0b6e77;
}

.back-btn:hover{
color:#7e2626d5;
}
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

<h1>Top / Least Selling Products Report</h1>

<a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout" class="back-btn">
← Back
</a>

</div>

<!-- FILTER -->

<form method="GET">

<div class="filter">

<label>Show</label>

<select name="limit">

<option value="">Select</option>

<option value="2">Top 2</option>
<option value="3">Top 3</option>
<option value="5">Top 5</option>
<option value="10">Top 10</option>

</select>

<select name="type">

<option value="">Select Type</option>

<option value="top" <?=($type=="top")?"selected":""?>>Top Selling</option>

<option value="low" <?=($type=="low")?"selected":""?>>Least Selling</option>

</select>

<button type="submit">Filter</button>
<a href="export_top_low_selling_product_pdf.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>" 
class="pdf-btn">
PDF
</a>
<a href="export_top_low_selling_product_excel.php?type=<?=$type?>&limit=<?=$limit?>" class="excel-btn">
Excel
</a>
</div>

</form>

<!-- TABLE -->

<table>

<thead>

<tr>
<th>Product ID</th>
<th>Product Name</th>
<th>Total Sold</th>
</tr>

</thead>

<tbody>

<?php

$currentCategory="";
$categoryTotal=0;
$grandTotal=0;

foreach($data as $row){

if($currentCategory != $row['Category_Name']){

if($currentCategory!=""){
?>

<tr style="background:#f7eaea;font-weight:bold;">
<td colspan="2" style="text-align:right">
Category Total
</td>
<td><?=$categoryTotal?></td>
</tr>

<?php
$categoryTotal=0;
}

$currentCategory=$row['Category_Name'];
?>

<tr class="category-header">

<td colspan="3">
Category : <?=$currentCategory?>
</td>

</tr>

<?php
}

?>

<tr>

<td><?=$row['Product_Id']?></td>

<td><?=$row['Product_Name']?></td>

<td><?=$row['Total_Sold']?></td>

</tr>

<?php

$categoryTotal += $row['Total_Sold'];
$grandTotal += $row['Total_Sold'];

}

/* LAST CATEGORY FOOTER */

if($currentCategory!=""){
?>

<tr style="background:#f7eaea;font-weight:bold;">
<td colspan="2" style="text-align:right">
Category Total
</td>
<td><?=$categoryTotal?></td>
</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>

<td colspan="2" style="text-align:right">
Grand Total Sold
</td>

<td><?=$grandTotal?></td>

</tr>

</tfoot>

</table>

</div>

</body>
</html>
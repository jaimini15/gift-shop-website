<?php
if (!isset($_SESSION))
session_start();

include(__DIR__ . '/../db.php');

$filterType = $_GET['type'] ?? '';
$limit = $_GET['limit'] ?? '';

/* ================= QUERY ================= */

$query = "
SELECT 
c.Category_Name,
p.Product_Id,
p.Product_Name,
s.Stock_Available,
s.Last_Update
FROM stock_details s
JOIN product_details p ON s.Product_Id = p.Product_Id
JOIN category_details c ON p.Category_Id = c.Category_Id
";

/* FILTER */

if($filterType=="high" && $limit){
$query .= " ORDER BY s.Stock_Available DESC LIMIT $limit";
}
elseif($filterType=="low" && $limit){
$query .= " ORDER BY s.Stock_Available ASC LIMIT $limit";
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

<title>Stock Report</title>

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
padding:15px 18px;
border-radius:6px;
margin-bottom:20px;
display:flex;
gap:10px;
align-items:end;
}
label{
    font-size:20px;
}
select{
padding:6px 8px;
border:1px solid #ddd;
border-radius:4px;
font-size:15px;
min-width:120px;
}

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
font-size:20px;
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

<h1>Stock Report</h1>

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
<option value="high">Highest Stock</option>
<option value="low">Lowest Stock</option>

</select>

<button type="submit">Filter</button>
<a href="export_top_selling_pdf.php?type=<?=$type?>&limit=<?=$limit?>" class="pdf-btn">
    PDF
</a>
<a href="export_order_excel.php?type=<?=$type?>&start=<?=$start?>&end=<?=$end?>" class="excel-btn">
Excel
</a>

<a href="export_stock_pdf.php?product_id=<?=$productFilter?>&period=<?=$periodFilter?>" 
class="pdf-btn">
PDF
</a>

<a href="export_stock_excel.php?type=<?=$filterType?>&limit=<?=$limit?>"
class="excel-btn">
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
<th>Last Update</th>
<th>Stock Available</th>
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
<td colspan="3" style="text-align:right">
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

<td colspan="4">
Category : <?=$currentCategory?>
</td>

</tr>

<?php
}

?>

<tr>

<td><?=$row['Product_Id']?></td>

<td><?=$row['Product_Name']?></td>

<td><?=date("d M Y",strtotime($row['Last_Update']))?></td>

<td><?=$row['Stock_Available']?></td>

</tr>

<?php

$categoryTotal += $row['Stock_Available'];
$grandTotal += $row['Stock_Available'];

}

/* LAST CATEGORY FOOTER */

if($currentCategory!=""){
?>

<tr style="background:#f7eaea;font-weight:bold;">
<td colspan="3" style="text-align:right">
Category Total
</td>
<td><?=$categoryTotal?></td>
</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>

<td colspan="3" style="text-align:right">
Grand Total Stock
</td>

<td><?=$grandTotal?></td>

</tr>

</tfoot>

</table>

</div>

</body>
</html>

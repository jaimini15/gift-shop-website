<?php
include("../db.php");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=stock_report.xls");
echo "\xEF\xBB\xBF";

$filterType = $_GET['type'] ?? '';
$limit = $_GET['limit'] ?? '';

$title = "Stock Report";

/* QUERY */

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
$title = "Highest Stock Report";
}
elseif($filterType=="low" && $limit){
$query .= " ORDER BY s.Stock_Available ASC LIMIT $limit";
$title = "Lowest Stock Report";
}
else{
$query .= " ORDER BY c.Category_Name, p.Product_Name";
}

$result = mysqli_query($connection,$query);

$data=[];
while($row=mysqli_fetch_assoc($result)){
$data[]=$row;
}

/* TABLE START */

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='4' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
$title
</td>
</tr>";

echo "<tr><td colspan='4'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;font-size:20px;text-align:center'>
<td style='padding:8px'>Product ID</td>
<td style='padding:8px'>Product Name</td>
<td style='padding:8px'>Last Update</td>
<td style='padding:8px'>Stock Available</td>
</tr>";

$currentCategory="";
$categoryTotal=0;
$grandTotal=0;

/* DATA */

foreach($data as $row){

if($currentCategory != $row['Category_Name']){

if($currentCategory!=""){

echo "<tr style='font-size:18px;font-weight:bold'>
<td colspan='3' style='text-align:right;padding:8px'>
Category Total
</td>
<td style='padding:8px'>$categoryTotal</td>
</tr>";

$categoryTotal=0;
}

$currentCategory=$row['Category_Name'];

echo "<tr style='font-size:20px;font-weight:bold'>
<td colspan='4' style='padding:8px;text-align:left'>
Category : $currentCategory
</td>
</tr>";

}

/* PRODUCT ROW */

$date = date("d-m-Y",strtotime($row['Last_Update']));

echo "<tr style='font-size:18px;text-align:center'>
<td style='padding:6px'>{$row['Product_Id']}</td>
<td style='padding:6px'>{$row['Product_Name']}</td>
<td style='padding:6px'>$date</td>
<td style='padding:6px'>{$row['Stock_Available']}</td>
</tr>";

$categoryTotal += $row['Stock_Available'];
$grandTotal += $row['Stock_Available'];

}

/* LAST CATEGORY TOTAL */

if($currentCategory!=""){

echo "<tr style='font-size:18px;font-weight:bold'>
<td colspan='3' style='text-align:right;padding:8px'>
Category Total
</td>
<td style='padding:8px'>$categoryTotal</td>
</tr>";

}

/* GRAND TOTAL */

echo "<tr style='font-size:20px;font-weight:bold'>
<td colspan='3' style='text-align:right;padding:10px'>
Grand Total Stock
</td>
<td style='padding:10px'>$grandTotal</td>
</tr>";

echo "</table>";
?>
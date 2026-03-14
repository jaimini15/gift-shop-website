<?php
include("../db.php");

/* UTF-8 for ₹ or special characters */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=top_low_selling_products_report.xls");
echo "\xEF\xBB\xBF";

$type  = $_GET['type'] ?? '';
$limit = $_GET['limit'] ?? '';

$title = "Top / Least Selling Products Report";

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
$title = "Top $limit Selling Products";

}
elseif($type=="low" && $limit){

$query .= " ORDER BY Total_Sold ASC LIMIT $limit";
$title = "Least $limit Selling Products";

}
else{

$query .= " ORDER BY c.Category_Name, p.Product_Name";

}

$result = mysqli_query($connection,$query);

/* ================= TABLE ================= */

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='3' style='font-size:22px;font-weight:bold;text-align:center;padding:10px'>
$title
</td>
</tr>";

echo "<tr><td colspan='3'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;text-align:center'>
<td>Product ID</td>
<td>Product Name</td>
<td>Total Sold</td>
</tr>";

$currentCategory="";
$categoryTotal=0;
$grandTotal=0;

while($row=mysqli_fetch_assoc($result)){

if($currentCategory != $row['Category_Name']){

if($currentCategory!=""){

echo "<tr>
<td colspan='2' style='text-align:right;font-weight:bold'>
Category Total
</td>
<td>$categoryTotal</td>
</tr>";

$categoryTotal=0;

}

$currentCategory = $row['Category_Name'];

echo "<tr>
<td colspan='3' style='font-weight:bold'>
Category : $currentCategory
</td>
</tr>";

}

echo "<tr>
<td>{$row['Product_Id']}</td>
<td>{$row['Product_Name']}</td>
<td>{$row['Total_Sold']}</td>
</tr>";

$categoryTotal += $row['Total_Sold'];
$grandTotal += $row['Total_Sold'];

}

/* LAST CATEGORY TOTAL */

if($currentCategory!=""){

echo "<tr>
<td colspan='2' style='text-align:right;font-weight:bold'>
Category Total
</td>
<td>$categoryTotal</td>
</tr>";

}

/* GRAND TOTAL */

echo "<tr>
<td colspan='2' style='text-align:right;font-weight:bold'>
Grand Total Sold
</td>
<td>$grandTotal</td>
</tr>";

echo "</table>";
?>
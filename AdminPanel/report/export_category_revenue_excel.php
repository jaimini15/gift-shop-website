<?php
include("../db.php");

/* UTF-8 FIX for ₹ symbol */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=category_revenue_report.xls");
echo "\xEF\xBB\xBF";

$period = $_GET['period'] ?? '';
$month  = $_GET['month'] ?? '';

$where = "";
$title = "Category Revenue Report";

/* ================= FILTER ================= */

if($period=="daily"){
$where="WHERE DATE(o.Order_Date)=CURDATE()";
$title="Daily Category Revenue Report";
}

elseif($period=="weekly"){
$where="WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)";
$title="Weekly Category Revenue Report";
}

elseif($period=="monthly"){
$where="WHERE MONTH(o.Order_Date)=MONTH(CURDATE())
AND YEAR(o.Order_Date)=YEAR(CURDATE())";
$title="Monthly Category Revenue Report";
}

elseif($period=="yearly"){

if($month){
$where="WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$month'";
$title="Category Revenue Report (Month $month)";
}
else{
$where="WHERE YEAR(o.Order_Date)=YEAR(CURDATE())";
$title="Yearly Category Revenue Report";
}

}

/* ================= QUERY ================= */

$query=mysqli_query($connection,"

SELECT 
c.Category_Name,
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
DATE(o.Order_Date) AS order_date,
p.Product_Name,
(oi.Quantity * oi.Price_Snapshot) AS revenue

FROM order_item oi

JOIN product_details p 
ON oi.Product_Id=p.Product_Id

JOIN category_details c 
ON p.Category_Id=c.Category_Id

JOIN `order` o 
ON oi.Order_Id=o.Order_Id

JOIN user_details u 
ON o.User_Id=u.User_Id

$where

ORDER BY c.Category_Name , o.Order_Date DESC

");

/* ================= TABLE ================= */

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='5' style='font-size:22px;font-weight:bold;text-align:center;padding:10px'>
$title
</td>
</tr>";

echo "<tr><td colspan='5'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;text-align:center'>
<td>Order ID</td>
<td>Customer Name</td>
<td>Order Date</td>
<td>Product Name</td>
<td>Revenue</td>
</tr>";

$currentCategory="";
$totalRevenue=0;

/* DATA */

while($row=mysqli_fetch_assoc($query)){

if($currentCategory != $row['Category_Name']){

$currentCategory=$row['Category_Name'];

echo "<tr>
<td colspan='5' style='font-weight:bold'>
Category : $currentCategory
</td>
</tr>";

}

$amount=number_format($row['revenue'],2);

echo "<tr>
<td>{$row['Order_Id']}</td>
<td>{$row['customer']}</td>
<td>{$row['order_date']}</td>
<td>{$row['Product_Name']}</td>
<td>₹ $amount</td>
</tr>";

$totalRevenue += $row['revenue'];

}

/* TOTAL */

echo "<tr>
<td colspan='4' style='text-align:right;font-weight:bold'>
Total Revenue
</td>
<td>₹ ".number_format($totalRevenue,2)."</td>
</tr>";

echo "</table>";

?>
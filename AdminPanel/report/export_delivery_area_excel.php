<?php
include("../db.php");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=delivery_area_report.xls");
echo "\xEF\xBB\xBF";

$period = $_GET['period'] ?? 'yearly';
$month  = $_GET['month'] ?? '';

$where = "";
$title = "Area-wise Orders Report";

/* FILTER */

if($period=="daily"){

$where = "WHERE DATE(o.Order_Date)=CURDATE()";
$title = "Daily Area-wise Orders Report";

}

elseif($period=="weekly"){

$where = "WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)";
$title = "Weekly Area-wise Orders Report";

}

elseif($period=="monthly"){

$where = "WHERE MONTH(o.Order_Date)=MONTH(CURDATE())
AND YEAR(o.Order_Date)=YEAR(CURDATE())";

$title = "Monthly Area-wise Orders Report";

}

elseif($period=="yearly"){

if($month){

$where = "WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$month'";

$title = "Area-wise Orders Report (Month $month)";

}else{

$where = "WHERE YEAR(o.Order_Date)=YEAR(CURDATE())";

$title = "Yearly Area-wise Orders Report";

}

}



$query = mysqli_query($connection,"

SELECT 
a.Area_Name,
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
DATE(o.Order_Date) AS order_date

FROM `order` o

JOIN user_details u 
ON o.User_Id=u.User_Id

JOIN area_details a 
ON u.Area_Id=a.Area_Id

$where

ORDER BY a.Area_Name , o.Order_Date DESC

");

/*TABLE*/

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
<td>Order ID</td>
<td>Customer Name</td>
<td>Order Date</td>
</tr>";

$currentArea = "";

while($row=mysqli_fetch_assoc($query)){

if($currentArea != $row['Area_Name']){

$currentArea = $row['Area_Name'];

echo "<tr>
<td colspan='3' style='font-weight:bold'>
Area : $currentArea
</td>
</tr>";

}

echo "<tr>
<td>{$row['Order_Id']}</td>
<td>{$row['customer']}</td>
<td>{$row['order_date']}</td>
</tr>";

}

echo "</table>";

?>
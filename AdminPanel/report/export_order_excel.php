<?php
include("../db.php");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=sales_report.xls");
echo "\xEF\xBB\xBF";

$type  = $_GET['type'] ?? '';
$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

$tableWhere = "WHERE 1";
$title = "Sales Report";

if($type=="daily"){
$tableWhere .= " AND DATE(Order_Date)=CURDATE()";
$title = "Daily Sales Report";
}

elseif($type=="weekly"){
$tableWhere .= " AND YEARWEEK(Order_Date,1)=YEARWEEK(CURDATE(),1)";
$title = "Weekly Sales Report";
}

elseif($type=="monthly"){
$tableWhere .= " AND MONTH(Order_Date)=MONTH(CURDATE()) 
AND YEAR(Order_Date)=YEAR(CURDATE())";
$title = "Monthly Sales Report";
}

elseif($type=="yearly"){
$tableWhere .= " AND YEAR(Order_Date)=YEAR(CURDATE())";
$title = "Yearly Sales Report";
}

elseif($start && $end){
$tableWhere .= " AND DATE(Order_Date) BETWEEN '$start' AND '$end'";
$title = "Sales Report ($start to $end)";
}

$query = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
o.Total_Amount,
c.First_Name,
c.Last_Name
FROM `order` o
JOIN user_details c 
ON o.User_Id=c.User_Id
$tableWhere
ORDER BY o.Order_Date DESC
");

/*TABLE*/

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='4' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
$title
</td>
</tr>";

echo "<tr><td colspan='4'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;text-align:center'>
<td>Order ID</td>
<td>Customer Name</td>
<td>Order Date</td>
<td>Total Amount</td>
</tr>";

$totalRevenue = 0;

/* DATA */

while($row=mysqli_fetch_assoc($query)){

$name = $row['First_Name']." ".$row['Last_Name'];
$date = date("d-m-Y",strtotime($row['Order_Date']));
$amount = number_format($row['Total_Amount'],2);

echo "<tr>
<td>{$row['Order_Id']}</td>
<td>$name</td>
<td>$date</td>
<td>₹ $amount</td>
</tr>";

$totalRevenue += $row['Total_Amount'];

}

/* TOTAL */

echo "<tr>
<td colspan='3' style='text-align:right;font-weight:bold'>
Total Revenue
</td>
<td>₹ ".number_format($totalRevenue,2)."</td>
</tr>";

echo "</table>";
?>
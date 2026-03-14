<?php
include("../db.php");

/* UTF-8 FIX */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=orders_report.xls");
echo "\xEF\xBB\xBF"; // UTF-8 BOM for ₹ symbol

$type  = $_GET['type'] ?? '';
$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

$tableWhere = "WHERE 1";
$title = "Orders Report";

/* FILTER LOGIC */

if($type=="daily"){
$tableWhere .= " AND DATE(Order_Date)=CURDATE()";
$title = "Daily Orders Report";
}

elseif($type=="weekly"){
$tableWhere .= " AND YEARWEEK(Order_Date,1)=YEARWEEK(CURDATE(),1)";
$title = "Weekly Orders Report";
}

elseif($type=="monthly"){
$tableWhere .= " AND MONTH(Order_Date)=MONTH(CURDATE()) AND YEAR(Order_Date)=YEAR(CURDATE())";
$title = "Monthly Orders Report";
}

elseif($type=="yearly"){
$tableWhere .= " AND YEAR(Order_Date)=YEAR(CURDATE())";
$title = "Yearly Orders Report";
}

elseif($start && $end){
$tableWhere .= " AND DATE(Order_Date) BETWEEN '$start' AND '$end'";
$title = "Orders Report ($start to $end)";
}


/* QUERY */

$query = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
o.Total_Amount,
c.First_Name,
c.Last_Name
FROM `order` o
JOIN user_details c ON o.User_Id=c.User_Id
$tableWhere
ORDER BY o.Order_Date DESC
");


echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* ===== TITLE ROW ===== */

echo "<tr>
<td colspan='4' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
$title
</td>
</tr>";

/* EMPTY ROW */

echo "<tr><td colspan='4'></td></tr>";

/* ===== COLUMN HEADERS ===== */

echo "<tr style='font-weight:bold;background:#dddddd;font-size:20px;text-align:center'>
<td style='padding:8px'>Order ID</td>
<td style='padding:8px'>Customer Name</td>
<td style='padding:8px'>Order Date</td>
<td style='padding:8px'>Total Amount</td>
</tr>";

/* ===== DATA ===== */

while($row=mysqli_fetch_assoc($query)){

$name = $row['First_Name']." ".$row['Last_Name'];
$date = date("d-m-Y",strtotime($row['Order_Date']));
$amount = number_format($row['Total_Amount'],2);

echo "<tr style='font-size:18px;text-align:center'>
<td style='padding:6px'>{$row['Order_Id']}</td>
<td style='padding:6px'>$name</td>
<td style='padding:6px'>$date</td>
<td style='padding:6px'>₹ $amount</td>
</tr>";

}

echo "</table>";
?>
<?php
include("../db.php");

/* UTF-8 FIX */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=delivery_status_report.xls");
echo "\xEF\xBB\xBF"; // for ₹ symbol

$statusFilter = $_GET['status'] ?? '';

$title = "Delivery Status Report";

/* FILTER */

$whereCondition = "";

if($statusFilter != ""){

if($statusFilter=="CONFIRM"){
$whereCondition = "WHERE d.Delivery_Status IS NULL";
$title = "Confirm Orders Report";
}
else{
$whereCondition = "WHERE d.Delivery_Status='$statusFilter'";
$title = "$statusFilter Orders Report";
}

}

/* QUERY */

$query = mysqli_query($connection,"

SELECT 
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
d.Delivery_Address,
a.Area_Name,

/* ✅ DELIVERY BOY */
CONCAT(db.First_Name,' ',db.Last_Name) AS delivery_boy,

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

/* 🔥 DELIVERY BOY JOIN */
LEFT JOIN delivery_area_map dam
ON dam.area_id = d.Area_Id AND dam.status='ACTIVE'

LEFT JOIN user_details db
ON dam.delivery_boy_id = db.User_Id

$whereCondition

ORDER BY o.Order_Date DESC

");


/* TABLE */

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='8' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
$title
</td>
</tr>";

echo "<tr><td colspan='8'></td></tr>";


/* HEADERS */

echo "<tr style='font-weight:bold;font-size:20px;text-align:center'>
<td style='padding:8px'>Order ID</td>
<td style='padding:8px'>Customer Name</td>
<td style='padding:8px'>Address</td>
<td style='padding:8px'>Area</td>
<td style='padding:8px'>Delivery Boy</td>
<td style='padding:8px'>Order Date</td>
<td style='padding:8px'>Amount</td>
<td style='padding:8px'>Status</td>
</tr>";


$totalOrders = 0;

/* DATA */

if(mysqli_num_rows($query) > 0){

while($row=mysqli_fetch_assoc($query)){

$date = date("d-m-Y",strtotime($row['order_date']));
$amount = number_format($row['Total_Amount'],2);

echo "<tr style='font-size:18px;text-align:center'>
<td style='padding:6px'>{$row['Order_Id']}</td>
<td style='padding:6px'>{$row['customer']}</td>
<td style='padding:6px'>{$row['Delivery_Address']}</td>
<td style='padding:6px'>{$row['Area_Name']}</td>
<td style='padding:6px'>".($row['delivery_boy'] ?? 'Not Assigned')."</td>
<td style='padding:6px'>$date</td>
<td style='padding:6px'>₹ $amount</td>
<td style='padding:6px'>{$row['status']}</td>
</tr>";
$totalOrders++;

}

}else{

echo "<tr style='font-size:18px;text-align:center'>
<td colspan='8'>No Orders Found</td>
</tr>";

}

/* TOTAL */

echo "<tr style='font-size:20px;font-weight:bold;text-align:right'>
<td colspan='7' style='padding:8px'>Total Orders</td>
<td style='padding:8px'>$totalOrders</td>
</tr>";


echo "</table>";
?>
<?php
include("../db.php");

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=year_wise_revenue_report.xls");
echo "\xEF\xBB\xBF";

$query = mysqli_query($connection,"
SELECT 
YEAR(Order_Date) AS year,
COUNT(Order_Id) AS total_orders,
SUM(Total_Amount) AS total_revenue
FROM `order`
WHERE Status='CONFIRM'
GROUP BY YEAR(Order_Date)
ORDER BY YEAR(Order_Date)
");

$totalOrders = 0;
$totalRevenue = 0;

/*  TABLE  */

echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='3' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
Year Wise Revenue Report
</td>
</tr>";

echo "<tr><td colspan='3'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;text-align:center'>
<td>Year</td>
<td>Total Orders</td>
<td>Total Revenue</td>
</tr>";

/* DATA */

while($row=mysqli_fetch_assoc($query)){

$year = $row['year'];
$orders = $row['total_orders'];
$revenue = number_format($row['total_revenue'],2);

echo "<tr>
<td>$year</td>
<td>$orders</td>
<td>₹ $revenue</td>
</tr>";

$totalOrders += $orders;
$totalRevenue += $row['total_revenue'];

}

/* TOTAL */

echo "<tr>
<td colspan='2' style='text-align:right;font-weight:bold'>
Total Revenue
</td>
<td>₹ ".number_format($totalRevenue,2)."</td>
</tr>";

echo "</table>";

?>
<?php
include("../db.php");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=product_sales_report.xls");
echo "\xEF\xBB\xBF"; 

$productFilter = $_GET['product_id'] ?? '';
$periodFilter  = $_GET['period'] ?? '';

$title = "Product-wise Sales Report";
$periodCondition = "";

if($periodFilter=="daily"){
$periodCondition = "AND DATE(o.Order_Date)=CURDATE()";
$title = "Daily Product-wise Sales Report";
}

elseif($periodFilter=="weekly"){
$periodCondition = "AND YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)";
$title = "Weekly Product-wise Sales Report";
}

elseif($periodFilter=="monthly"){
$periodCondition = "AND MONTH(o.Order_Date)=MONTH(CURDATE()) 
                    AND YEAR(o.Order_Date)=YEAR(CURDATE())";
$title = "Monthly Product-wise Sales Report";
}

elseif($periodFilter=="yearly"){
$periodCondition = "AND YEAR(o.Order_Date)=YEAR(CURDATE())";
$title = "Yearly Product-wise Sales Report";
}


/* PRODUCT NAME */

$productName = "";

if($productFilter){
$q = mysqli_query($connection,"
SELECT Product_Name 
FROM product_details 
WHERE Product_Id='$productFilter'
");

if($row=mysqli_fetch_assoc($q)){
$productName = $row['Product_Name'];
}
}


/* QUERY */

$query = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
u.First_Name,
u.Last_Name
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
JOIN user_details u ON o.User_Id=u.User_Id
WHERE oi.Product_Id='$productFilter'
$periodCondition
GROUP BY o.Order_Id
ORDER BY o.Order_Date DESC
");


echo "<table border='1' style='border-collapse:collapse;font-family:Segoe UI;'>";

/* TITLE */

echo "<tr>
<td colspan='4' style='font-size:25px;font-weight:bold;text-align:center;padding:12px'>
$title
</td>
</tr>";

/* PRODUCT NAME */

echo "<tr>
<td colspan='4' style='font-size:18px;font-weight:bold;text-align:left;padding:8px'>
Product : $productName
</td>
</tr>";

/* EMPTY ROW */

echo "<tr><td colspan='4'></td></tr>";

/* HEADERS */

echo "<tr style='font-weight:bold;background:#dddddd;font-size:20px;text-align:center'>
<td style='padding:8px'>Order ID</td>
<td style='padding:8px'>Customer Name</td>
<td style='padding:8px'>Order Date</td>
<td style='padding:8px'>Revenue</td>
</tr>";

$totalRevenue = 0;

/* DATA */

while($row=mysqli_fetch_assoc($query)){

$name = $row['First_Name']." ".$row['Last_Name'];
$date = date("d-m-Y",strtotime($row['Order_Date']));
$amount = number_format($row['revenue'],2);

$totalRevenue += $row['revenue'];

echo "<tr style='font-size:18px;text-align:center'>
<td style='padding:6px'>{$row['Order_Id']}</td>
<td style='padding:6px'>$name</td>
<td style='padding:6px'>$date</td>
<td style='padding:6px'>₹ $amount</td>
</tr>";

}


/* TOTAL ROW */

echo "<tr style='font-size:20px;font-weight:bold;text-align:right'>
<td colspan='3' style='padding:8px'>Total Revenue</td>
<td style='padding:8px'>₹ ".number_format($totalRevenue,2)."</td>
</tr>";

echo "</table>";
?>
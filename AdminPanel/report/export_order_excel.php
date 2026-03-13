<?php
include("../db.php");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sales_report.xls");

$query=mysqli_query($connection,"
SELECT Order_Id,Order_Date,Total_Amount
FROM `order`
");

echo "Order ID\tDate\tAmount\n";

while($row=mysqli_fetch_assoc($query)){

echo $row['Order_Id']."\t";
echo $row['Order_Date']."\t";
echo $row['Total_Amount']."\n";

}
?>
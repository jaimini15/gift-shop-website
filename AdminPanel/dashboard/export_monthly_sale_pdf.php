<?php
if (!isset($_SESSION)) session_start();

require_once '../../dompdf/autoload.inc.php';
$logo = __DIR__ . "/../../home page/logo.svg";
use Dompdf\Dompdf;
use Dompdf\Options;

include(__DIR__ . '/../db.php');

$from = $_GET['from_date'] ?? '';
$to   = $_GET['to_date'] ?? '';

$where = "";
if (!empty($from) && !empty($to)) {
    $where = "WHERE DATE(o.Order_Date) BETWEEN '$from' AND '$to'";
}

$summaryQuery = mysqli_query($connection,"
SELECT 
COUNT(o.Order_Id) total_orders,
SUM(o.Total_Amount) total_sales
FROM `order` o
$where
");

$summary = mysqli_fetch_assoc($summaryQuery);
$totalOrders = $summary['total_orders'] ?? 0;
$totalSales  = $summary['total_sales'] ?? 0;

$monthlyQuery = mysqli_query($connection,"
SELECT 
YEAR(o.Order_Date) year,
MONTH(o.Order_Date) month,
COUNT(o.Order_Id) total_orders,
SUM(o.Total_Amount) total_sales
FROM `order` o
$where
GROUP BY YEAR(o.Order_Date), MONTH(o.Order_Date)
ORDER BY YEAR(o.Order_Date), MONTH(o.Order_Date)
");

$orderQuery = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
o.Total_Amount,
u.First_Name,
u.Last_Name
FROM `order` o
LEFT JOIN user_details u ON o.User_Id = u.User_Id
$where
ORDER BY o.Order_Date DESC
");

// $logo = "../../home%20page/logo.svg";
$chart = "../charts/sales_chart.png";

$html = '

<style>

body{
font-family:DejaVu Sans;
font-size:11px;
margin:25px;
color:#333;
}

.header{
border-bottom:2px solid #7e2626;
padding-bottom:10px;
}

.logo{
width:80px;
}

.company{
font-size:18px;
font-weight:bold;
color:#7e2626;
}

.info{
font-size:10px;
color:#555;
}

.title{
margin-top:15px;
font-size:16px;
text-align:center;
font-weight:bold;
color:#7e2626;
}

.summary{
margin-top:20px;
}

.summary td{
border:1px solid #ddd;
padding:10px;
text-align:center;
}

.summary h3{
margin:0;
font-size:13px;
color:#7e2626;
}

.summary p{
margin:5px 0 0 0;
font-size:14px;
font-weight:bold;
}

.section{
margin-top:25px;
font-size:13px;
font-weight:bold;
color:#7e2626;
border-bottom:1px solid #7e2626;
padding-bottom:4px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

th{
background:#7e2626;
color:#fff;
padding:6px;
font-size:11px;
}

td{
padding:6px;
border-bottom:1px solid #ddd;
font-size:10px;
}

.footer{
margin-top:30px;
font-size:9px;
text-align:center;
color:#777;
}

</style>

<table class="header" width="100%">
<tr>
<td width="15%">

</td>
<td width="85%">
<div class="company">GiftShop</div>
<div class="info">
201/A, Maninagar, Ahmedabad<br>
Email: giftshopmanigar@gmail.com | Phone: 9876543210
</div>
</td>
</tr>
</table>

<div class="title">Sales Report</div>

<div style="margin-top:10px;font-size:10px;">
<strong>Generated:</strong> '.date("d M Y H:i").'<br>';

if(!empty($from) && !empty($to)){
$html .= '<strong>Period:</strong> '.$from.' to '.$to;
}

$html .= '</div>

<table class="summary">
<tr>
<td>
<h3>Total Orders</h3>
<p>'.$totalOrders.'</p>
</td>
<td>
<h3>Total Sales</h3>
<p>₹ '.number_format($totalSales,2).'</p>
</td>
<td>
<h3>Report Range</h3>
<p>'.($from ?: "All Time").'</p>
</td>
</tr>
</table>

<div class="section">Monthly Sales Summary</div>

<table>
<tr>
<th>Month</th>
<th>Total Orders</th>
<th>Total Sales</th>
</tr>';

while($m=mysqli_fetch_assoc($monthlyQuery)){
$monthName=date("F Y",mktime(0,0,0,$m['month'],1,$m['year']));
$html.='
<tr>
<td>'.$monthName.'</td>
<td>'.$m['total_orders'].'</td>
<td>₹ '.number_format($m['total_sales'],2).'</td>
</tr>';
}

$html.='</table>

<div class="section">Sales Chart</div>
<img src="'.$chart.'" style="width:100%;height:220px;">

<div class="section">Order Details</div>

<table>
<tr>
<th>Order ID</th>
<th>User Name</th>
<th>Date</th>
<th>Amount</th>
</tr>';

while($o=mysqli_fetch_assoc($orderQuery)){
$user=$o['First_Name']." ".$o['Last_Name'];
$html.='
<tr>
<td>'.$o['Order_Id'].'</td>
<td>'.$user.'</td>
<td>'.$o['Order_Date'].'</td>
<td>₹ '.number_format($o['Total_Amount'],2).'</td>
</tr>';
}

$html.='</table>

<div class="footer">
GiftShop Sales System Report
</div>
';

$options=new Options();
$options->set('isRemoteEnabled',true);

$dompdf=new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4","portrait");
$dompdf->render();
$dompdf->stream("GiftShop_Sales_Report.pdf",["Attachment"=>true]);
exit;
?>
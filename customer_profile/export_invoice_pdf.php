<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];
$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid Order ID");
}

$userRes = mysqli_query($connection,"
SELECT 
u.First_Name,
u.Address,
a.Area_Name,
a.Pincode
FROM user_details u
LEFT JOIN area_details a 
ON u.Area_Id = a.Area_Id
WHERE u.User_Id='$uid'
");

$user = mysqli_fetch_assoc($userRes);

$orderRes = mysqli_query($connection,"
SELECT 
o.Order_Id,
o.Order_Date,
o.Total_Amount
FROM `order` o
WHERE o.Order_Id='$orderId'
AND o.User_Id='$uid'
");

$order = mysqli_fetch_assoc($orderRes);

if (!$order) {
    die("Order not found");
}

/* PAYMENT METHOD */

$payment = mysqli_fetch_assoc(mysqli_query($connection,"
SELECT Payment_Method
FROM payment_details
WHERE Order_Id='$orderId'
"));

/* FETCH ITEMS  */

$itemRes = mysqli_query($connection,"
SELECT 
oi.Quantity,
oi.Price_Snapshot,
pd.Product_Name
FROM order_item oi
JOIN product_details pd
ON oi.Product_Id = pd.Product_Id
WHERE oi.Order_Id='$orderId'
");

$rows = "";
$subtotal = 0;

while ($row = mysqli_fetch_assoc($itemRes)) {

$total = $row['Quantity'] * $row['Price_Snapshot'];
$subtotal += $total;

$rows .= "
<tr>
<td>{$row['Product_Name']}</td>
<td>{$row['Quantity']}</td>
<td>₹".number_format($row['Price_Snapshot'],2)."</td>
<td>₹".number_format($total,2)."</td>
</tr>
";
}

$grandTotal = $order['Total_Amount'];

/* LOGO */

$logoPath = __DIR__ . "/../home page/logo.svg";

if(file_exists($logoPath)){
$logoData = base64_encode(file_get_contents($logoPath));
$logoPath = 'data:image/png;base64,' . $logoData;
}

$html = "

<style>

@page{
margin:20px 25px 80px 25px;
}

body{
font-family: DejaVu Sans, sans-serif;
font-size:12px;
color:#333;
}
.header{
border-bottom:2px solid #7e2626;
padding-bottom:8px;
margin-bottom:12px;
position:relative;
padding-left:80px;
min-height:70px;
}

.header img{
position:absolute;
left:-10px;
top:-10px;
height:50px;
}

.company h2{
margin:0;
color:#7e2626;
font-size:18px;
}

.company div{
font-size:11px;
margin-top:2px;
}
.invoice-meta{
position:absolute;
right:0;
top:0;
text-align:right;
font-size:11px;
margin-top:6px;
}

.section{
margin-top:10px;
padding:10px;
border:1px solid #ddd;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

th{
background:#7e2626;
color:white;
padding:7px;
font-size:12px;
}

td{
border:1px solid #ddd;
padding:6px;
font-size:11px;
text-align:center;
}

/* TOTAL BOX */

.total-box{
width:280px;
float:right;
margin-top:15px;
}

.total-row{
display:flex;
justify-content:space-between;
padding:4px 0;
}

.grand{
font-weight:bold;
border-top:1px solid #ddd;
padding-top:6px;
}

/* FOOTER */

.footer{
position:fixed;
bottom:-40px;
left:0;
right:0;
text-align:center;
font-size:10px;
border-top:1px solid #ccc;
color:#666;
}

</style>


<div class='header'>

<img src='$logoPath'>

<div class='company'>

<h2>GiftShop</h2>

<div>201/A,Business Park,GiftShop,Maninagar,Ahmedabad,Gujarat</div>
<div>Email: giftshopmaninagar@gmail.com | Phone:+91 9876543210</div>

</div>

</div>

<div class='invoice-meta'>
<b>Invoice</b><br>
Invoice #: {$order['Order_Id']}<br>
Date: ".date("d M Y", strtotime($order['Order_Date']))."
</div>

</div>


<div class='section'>

<b>Bill To</b><br><br>

{$user['First_Name']}<br>
{$user['Address']}<br>
{$user['Area_Name']} - {$user['Pincode']}<br><br>

<b>Payment Method:</b> ".($payment['Payment_Method'] ?? "N/A")."

</div>


<table>

<thead>

<tr>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
<th>Total</th>
</tr>

</thead>

<tbody>

$rows

</tbody>

</table>


<div class='total-box'>

<div class='total-row'>
<span>Subtotal</span>
<span>₹".number_format($subtotal,2)."</span>
</div>

<div class='total-row'>
<span>Shipping</span>
<span>₹0.00</span>
</div>

<div class='total-row grand'>
<span>Grand Total</span>
<span>₹".number_format($grandTotal,2)."</span>
</div>

</div>


<div style='clear:both'></div>


<div class='footer'>
Thank you for shopping with GiftShop | Generated ".date("d M Y h:i A")."
</div>

";

/* PDF GENERATE  */

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$dompdf->stream("Invoice_Order_".$order['Order_Id'].".pdf",array("Attachment"=>0));
<?php
if (!isset($_SESSION)) session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include(__DIR__ . '/../db.php');

/* ================= FILTER ================= */
$from = $_GET['from_date'] ?? '';
$to   = $_GET['to_date'] ?? '';

$where = "";
if (!empty($from) && !empty($to)) {
    $where = "WHERE DATE(Order_Date) BETWEEN '$from' AND '$to'";
}

/* ================= MONTHLY SUMMARY ================= */
$monthlyQuery = mysqli_query(
    $connection,
    "SELECT 
        YEAR(Order_Date) as year,
        MONTH(Order_Date) as month,
        COUNT(Order_Id) as total_orders,
        SUM(Total_Amount) as total_sales
     FROM `order`
     $where
     GROUP BY YEAR(Order_Date), MONTH(Order_Date)
     ORDER BY YEAR(Order_Date), MONTH(Order_Date)"
);

/* ================= ORDER RECORDS ================= */
$orderQuery = mysqli_query(
    $connection,
    "SELECT Order_Id, Order_Date, Total_Amount
     FROM `order`
     $where
     ORDER BY Order_Date DESC"
);

$totalOrders = mysqli_num_rows($orderQuery);

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">GiftShop Sales Report</h2>
<hr>
<p><strong>Total Orders:</strong> '.$totalOrders.'</p>';

if (!empty($from) && !empty($to)) {
    $html .= "<p><strong>From:</strong> $from &nbsp;&nbsp; <strong>To:</strong> $to</p>";
}

/* ===== MONTHLY TABLE ===== */
$html .= '
<h3>Monthly Summary</h3>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr>
<th>Month</th>
<th>Total Orders</th>
<th>Total Sales </th>
</tr>';

while ($m = mysqli_fetch_assoc($monthlyQuery)) {

    $monthName = date("F Y", mktime(0,0,0,$m['month'],1,$m['year']));

    $html .= '
    <tr>
        <td>'.$monthName.'</td>
        <td>'.$m['total_orders'].'</td>
        <td>'.number_format($m['total_sales'],2).'</td>
    </tr>';
}

$html .= '</table>';

/* ===== ORDER RECORDS ===== */
mysqli_data_seek($orderQuery, 0);

$html .= '
<h3 style="margin-top:30px;">Order Records</h3>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr>
<th>Order ID</th>
<th>Date</th>
<th>Amount</th>
</tr>';

while ($o = mysqli_fetch_assoc($orderQuery)) {

    $html .= '
    <tr>
        <td>'.$o['Order_Id'].'</td>
        <td>'.$o['Order_Date'].'</td>
        <td>'.number_format($o['Total_Amount'],2).'</td>
    </tr>';
}

$html .= '</table>';

/* ================= GENERATE PDF ================= */
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Monthly_order_report.pdf", ["Attachment" => true]);
exit;
?>
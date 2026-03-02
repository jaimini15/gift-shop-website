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
    $where = "WHERE DATE(o.Order_Date) BETWEEN '$from' AND '$to'";
}

/* ================= AREA WISE ORDERS ================= */
$query = mysqli_query($connection, "
    SELECT 
        a.Area_Name,
        COUNT(o.Order_Id) AS total_orders
    FROM `order` o
    JOIN user_details u ON o.User_Id = u.User_Id
    JOIN area_details a ON u.Area_Id = a.Area_Id
    $where
    GROUP BY a.Area_Id, a.Area_Name
    ORDER BY total_orders DESC
");

$totalOrders = 0;
$chartData = "";
$tableRows = "";

while ($row = mysqli_fetch_assoc($query)) {

    $totalOrders += $row['total_orders'];

    // For Google Pie Chart
    $chartData .= "|" . urlencode($row['Area_Name']) . " (" . $row['total_orders'] . ")";

    // Table rows
    $tableRows .= "
    <tr>
        <td>{$row['Area_Name']}</td>
        <td>{$row['total_orders']}</td>
    </tr>";
}

/* ================= GOOGLE PIE CHART ================= */
$chartURL = "https://image-charts.com/chart?cht=p&chs=500x300&chd=t:"
            . str_replace('|', ',', trim($chartData,'|'))
            . "&chl=" . trim($chartData,'|');

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">GiftShop Area Wise Orders Report</h2>
<hr>
<p><strong>Total Orders:</strong> '.$totalOrders.'</p>';

if (!empty($from) && !empty($to)) {
    $html .= "<p><strong>From:</strong> $from &nbsp;&nbsp; <strong>To:</strong> $to</p>";
}

$html .= '
<h3 style="margin-top:30px;">Orders by Delivery Area</h3>



<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr>
<th>Area</th>
<th>Total Orders</th>
</tr>
'.$tableRows.'
</table>
';

/* ================= GENERATE PDF ================= */
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Area_Wise_Orders_Report.pdf", ["Attachment" => true]);
exit;
?>
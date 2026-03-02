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

/* ================= CATEGORY REVENUE ================= */
$query = mysqli_query($connection, "
    SELECT 
        c.Category_Name,
        SUM(oi.Quantity * oi.Price_Snapshot) AS total_revenue
    FROM order_item oi
    JOIN product_details p ON oi.Product_Id = p.Product_Id
    JOIN category_details c ON p.Category_Id = c.Category_Id
    JOIN `order` o ON oi.Order_Id = o.Order_Id
    $where
    GROUP BY c.Category_Id, c.Category_Name
    ORDER BY total_revenue DESC
");

$totalRevenue = 0;
$chartData = "";
$tableRows = "";

while ($row = mysqli_fetch_assoc($query)) {

    $totalRevenue += $row['total_revenue'];

    // For Google Pie Chart
    $chartData .= "|" . urlencode($row['Category_Name']) . " (" . round($row['total_revenue']) . ")";

    // Table row
    $tableRows .= "
    <tr>
        <td>{$row['Category_Name']}</td>
        <td>".number_format($row['total_revenue'],2)."</td>
    </tr>";
}

/* ================= GOOGLE PIE CHART ================= */
$chartURL = "https://image-charts.com/chart?cht=p&chs=500x300&chd=t:"
            . str_replace('|', ',', trim($chartData,'|'))
            . "&chl=" . trim($chartData,'|');

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">GiftShop Category Revenue Report</h2>
<hr>
<p><strong>Total Revenue:</strong> '.number_format($totalRevenue,2).'</p>';

if (!empty($from) && !empty($to)) {
    $html .= "<p><strong>From:</strong> $from &nbsp;&nbsp; <strong>To:</strong> $to</p>";
}

$html .= '
<h3 style="margin-top:30px;">Category Wise Revenue</h3>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr>
<th>Category</th>
<th>Total Revenue</th>
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

$dompdf->stream("Category_Revenue_Report.pdf", ["Attachment" => true]);
exit;
?>
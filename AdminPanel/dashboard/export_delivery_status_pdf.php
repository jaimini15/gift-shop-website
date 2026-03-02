<?php
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include(__DIR__ . '/../db.php');

/* ================= DELIVERY STATUS QUERY ================= */
$statusQuery = mysqli_query($connection, "
    SELECT 
        Delivery_Status,
        COUNT(*) AS total_count
    FROM delivery_details
    GROUP BY Delivery_Status
");

/* Initialize counts */
$packed = 0;
$outForDelivery = 0;
$delivered = 0;

while($row = mysqli_fetch_assoc($statusQuery)) {

    if($row['Delivery_Status'] == 'Packed') {
        $packed = $row['total_count'];
    }
    elseif($row['Delivery_Status'] == 'Out for Delivery') {
        $outForDelivery = $row['total_count'];
    }
    elseif($row['Delivery_Status'] == 'Delivered') {
        $delivered = $row['total_count'];
    }
}

$totalOrders = $packed + $outForDelivery + $delivered;

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">Delivery Status Report</h2>
<hr>

<p><strong>Total Orders:</strong> '.$totalOrders.'</p>

<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Status</th>
    <th>Total Orders</th>
</tr>

<tr>
    <td>Packed</td>
    <td>'.$packed.'</td>
</tr>

<tr>
    <td>Out for Delivery</td>
    <td>'.$outForDelivery.'</td>
</tr>

<tr>
    <td>Delivered</td>
    <td>'.$delivered.'</td>
</tr>

</table>
';

/* ================= GENERATE PDF ================= */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

/* ================= DOWNLOAD PDF ================= */
$dompdf->stream("Delivery_Status_Report.pdf", ["Attachment" => true]);
exit;
?>
<?php
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include(__DIR__ . '/../db.php');

/* ================= GET ALL CONFIRMED ORDERS ================= */
$query = mysqli_query($connection, "
    SELECT 
        YEAR(Order_Date) AS year,
        Order_Id,
        Order_Date,
        Total_Amount,
        User_Id
    FROM `order`
    WHERE Status='CONFIRM'
    ORDER BY YEAR(Order_Date), Order_Date
");

/* ================= BUILD HTML ================= */
$html = '<h2 style="text-align:center;">Year Wise Revenue Report</h2><hr>';

$currentYear = null;
$yearTotal = 0;
$grandTotal = 0;

while($row = mysqli_fetch_assoc($query)){

    // Start a new year section
    if($currentYear !== $row['year']){
        if($currentYear !== null){
            // Close previous year table with total
            $html .= '
            <tr style="font-weight:bold; background:#e6e6e6;">
                <td colspan="3">Total Revenue for '.$currentYear.'</td>
                <td>'.number_format($yearTotal,2).'</td>
            </tr>
            </table><br>';
        }

        $currentYear = $row['year'];
        $yearTotal = 0;

        $html .= '<h3>Year: '.$currentYear.'</h3>';
        $html .= '
        <table border="1" width="100%" cellpadding="6" cellspacing="0">
        <tr style="background:#f2f2f2;">
            <th>Order ID</th>
            <th>Order Date</th>
            <th>User ID</th>
            <th>Amount </th>
        </tr>';
    }

    $yearTotal += $row['Total_Amount'];
    $grandTotal += $row['Total_Amount'];

    $html .= '
    <tr>
        <td>'.$row['Order_Id'].'</td>
        <td>'.$row['Order_Date'].'</td>
        <td>'.$row['User_Id'].'</td>
        <td>'.number_format($row['Total_Amount'],2).'</td>
    </tr>';
}

// Last year total
if($currentYear !== null){
    $html .= '
    <tr style="font-weight:bold; background:#e6e6e6;">
        <td colspan="3">Total Revenue for '.$currentYear.'</td>
        <td>'.number_format($yearTotal,2).'</td>
    </tr>
    </table><br>';
}

// Grand total
$html .= '<h3 style="text-align:right;">Grand Total Revenue: '.number_format($grandTotal,2).'</h3>';

/* ================= GENERATE PDF ================= */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

/* ================= DOWNLOAD PDF ================= */
$dompdf->stream("Year_Wise_Revenue_Report.pdf", ["Attachment" => true]);
exit;
?>
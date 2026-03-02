<?php
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include(__DIR__ . '/../db.php');

/* ================= PRODUCTS WITHOUT SALES QUERY ================= */
$unsoldQuery = mysqli_query($connection, "
    SELECT 
        p.Product_Id,
        p.Product_Name,
        p.Price
    FROM product_details p
    LEFT JOIN order_item oi 
        ON p.Product_Id = oi.Product_Id
    WHERE oi.Product_Id IS NULL
    ORDER BY p.Product_Name ASC
");

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">Products Without Sales Report</h2>
<hr>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Product ID</th>
    <th>Product Name</th>
    <th>Price </th>
</tr>';

if(mysqli_num_rows($unsoldQuery) > 0){
    while($row = mysqli_fetch_assoc($unsoldQuery)){
        $html .= '
        <tr>
            <td>'.$row['Product_Id'].'</td>
            <td>'.$row['Product_Name'].'</td>
            <td>'.number_format($row['Price'],2).'</td>
        </tr>';
    }
} else {
    $html .= '
    <tr>
        <td colspan="3" align="center">All products have sales</td>
    </tr>';
}

$html .= '</table>';

/* ================= GENERATE PDF ================= */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

/* ================= DOWNLOAD PDF ================= */
$dompdf->stream("Products_Without_Sales_Report.pdf", ["Attachment" => true]);
exit;
?>
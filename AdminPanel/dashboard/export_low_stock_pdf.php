<?php
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include(__DIR__ . '/../db.php');

$lowStockQuery = mysqli_query($connection, "
    SELECT 
        p.Product_Id,
        p.Product_Name,
        p.Price,
        s.Stock_Available,
        s.Last_Update
    FROM stock_details s
    JOIN product_details p 
        ON s.Product_Id = p.Product_Id
    WHERE s.Stock_Available < 5
    ORDER BY s.Stock_Available ASC
");

$html = '
<h2 style="text-align:center;">Low Stock Products Report</h2>
<hr>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Product Name</th>
    <th>Price </th>
    <th>Stock</th>
    <th>Last Update</th>
</tr>';

if(mysqli_num_rows($lowStockQuery) > 0){
    while($row = mysqli_fetch_assoc($lowStockQuery)){
        $html .= '
        <tr>
            <td>'.$row['Product_Name'].'</td>
            <td>'.number_format($row['Price'],2).'</td>
            <td>'.$row['Stock_Available'].'</td>
            <td>'.$row['Last_Update'].'</td>
        </tr>';
    }
} else {
    $html .= '
    <tr>
        <td colspan="4" align="center">All products have sufficient stock</td>
    </tr>';
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("Low_Stock_Products_Report.pdf", ["Attachment" => true]);
exit;
?>
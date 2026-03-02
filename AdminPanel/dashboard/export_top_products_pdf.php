<?php
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include(__DIR__ . '/../db.php');

/* ================= TOP SELLING PRODUCTS QUERY ================= */
$topProductsQuery = mysqli_query($connection, "
    SELECT 
        oi.Product_Id,
        p.Product_Name,
        SUM(oi.Quantity) AS total_sold,
        SUM(oi.Quantity * oi.Price_Snapshot) AS total_revenue
    FROM order_item oi
    JOIN product_details p 
        ON oi.Product_Id = p.Product_Id
    GROUP BY oi.Product_Id, p.Product_Name
    ORDER BY total_sold DESC
    LIMIT 10
");

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">Top Selling Products Report</h2>
<hr>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Product Name</th>
    <th>Quantity Sold</th>
    <th>Total Revenue (₹)</th>
</tr>';

if(mysqli_num_rows($topProductsQuery) > 0){
    while($row = mysqli_fetch_assoc($topProductsQuery)){
        $html .= '
        <tr>
            <td>'.$row['Product_Name'].'</td>
            <td>'.$row['total_sold'].'</td>
            <td>'.number_format($row['total_revenue'],2).'</td>
        </tr>';
    }
} else {
    $html .= '
    <tr>
        <td colspan="3" align="center">No product sales data available</td>
    </tr>';
}

$html .= '</table>';

/* ================= GENERATE PDF ================= */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

/* ================= DOWNLOAD PDF ================= */
$dompdf->stream("Top_selling_products.pdf", ["Attachment" => true]);
exit;
?>
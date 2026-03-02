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

/* ================= LOW SELLING PRODUCTS QUERY ================= */
$lowProductsQuery = mysqli_query($connection, "
    SELECT 
        p.Product_Id,
        p.Product_Name,
        IFNULL(SUM(oi.Quantity),0) AS total_sold,
        IFNULL(SUM(oi.Quantity * oi.Price_Snapshot),0) AS total_revenue
    FROM product_details p
    LEFT JOIN order_item oi 
        ON oi.Product_Id = p.Product_Id
    GROUP BY p.Product_Id, p.Product_Name
    ORDER BY total_sold ASC
    LIMIT 10
");

/* ================= BUILD HTML ================= */
$html = '
<h2 style="text-align:center;">Top & Low Selling Products Report</h2>
<hr>

<h3>Top Selling Products</h3>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Product Name</th>
    <th>Quantity Sold</th>
    <th>Total Revenue </th>
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

/* ================= LOW SELLING TABLE (ADDED BELOW) ================= */

$html .= '
<br><br>
<h3>Low Selling Products</h3>
<table border="1" width="100%" cellpadding="6" cellspacing="0">
<tr style="background:#f2f2f2;">
    <th>Product Name</th>
    <th>Quantity Sold</th>
    <th>Total Revenue </th>
</tr>';

if(mysqli_num_rows($lowProductsQuery) > 0){
    while($row = mysqli_fetch_assoc($lowProductsQuery)){
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
        <td colspan="3" align="center">No low selling product data available</td>
    </tr>';
}

$html .= '</table>';

/* ================= GENERATE PDF ================= */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

/* ================= DOWNLOAD PDF ================= */
$dompdf->stream("Export_Top_Low_Selling_Products_pdf.php.pdf", ["Attachment" => true]);
exit;
?>
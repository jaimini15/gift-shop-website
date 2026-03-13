<?php
require('../../fpdf/fpdf.php');
include("../db.php");

$product_id = $_GET['product_id'] ?? '';
$period = $_GET['period'] ?? '';

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Product Sales Report',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Generated: '.date("d/m/Y H:i"),0,1);

$pdf->Ln(5);

$pdf->Cell(60,10,'Period',1);
$pdf->Cell(60,10,'Orders',1);
$pdf->Cell(60,10,'Revenue',1);
$pdf->Ln();

$query=mysqli_query($connection,"
SELECT 
MONTHNAME(o.Order_Date) AS label,
SUM(oi.Quantity * oi.Price_Snapshot) AS revenue,
COUNT(DISTINCT o.Order_Id) AS orders
FROM order_item oi
JOIN `order` o ON oi.Order_Id=o.Order_Id
WHERE oi.Product_Id='$product_id'
GROUP BY MONTH(o.Order_Date)
");

while($row=mysqli_fetch_assoc($query)){

$pdf->Cell(60,10,$row['label'],1);
$pdf->Cell(60,10,$row['orders'],1);
$pdf->Cell(60,10,'Rs '.$row['revenue'],1);
$pdf->Ln();

}

$pdf->Output();

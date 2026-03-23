<?php
if (!isset($_SESSION))
    session_start();

include(__DIR__ . '/../db.php');

$periodFilter = $_GET['period'] ?? 'yearly';
$monthFilter = $_GET['month'] ?? '';

$labels = [];
$data = [];
$tableData = [];
$totalOrders = 0;

/* DAILY */

if ($periodFilter == "daily") {

    $query = "
SELECT 
a.Area_Name AS label,
COUNT(o.Order_Id) AS orders_count
FROM `order` o
JOIN user_details u ON o.User_Id=u.User_Id
JOIN area_details a ON u.Area_Id=a.Area_Id
WHERE DATE(o.Order_Date)=CURDATE()
GROUP BY a.Area_Id
";

}

/* WEEKLY*/ elseif ($periodFilter == "weekly") {

    $query = "
SELECT 
a.Area_Name AS label,
COUNT(o.Order_Id) AS orders_count
FROM `order` o
JOIN user_details u ON o.User_Id=u.User_Id
JOIN area_details a ON u.Area_Id=a.Area_Id
WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)
GROUP BY a.Area_Id
";

}

/* MONTHLY */ elseif ($periodFilter == "monthly") {

    $query = "
SELECT 
a.Area_Name AS label,
COUNT(o.Order_Id) AS orders_count
FROM `order` o
JOIN user_details u ON o.User_Id=u.User_Id
JOIN area_details a ON u.Area_Id=a.Area_Id
WHERE MONTH(o.Order_Date)=MONTH(CURDATE())
AND YEAR(o.Order_Date)=YEAR(CURDATE())
GROUP BY a.Area_Id
";

}

/*YEARLY */ 
elseif ($periodFilter == "yearly") {

    if ($monthFilter) {

        $query = "
SELECT 
a.Area_Name AS label,
COUNT(o.Order_Id) AS orders_count
FROM `order` o
JOIN user_details u ON o.User_Id=u.User_Id
JOIN area_details a ON u.Area_Id=a.Area_Id
WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$monthFilter'
GROUP BY a.Area_Id
";

    } else {

        $query = "
SELECT 
DATE_FORMAT(o.Order_Date,'%M') AS label,
MONTH(o.Order_Date) AS month_no,
COUNT(o.Order_Id) AS orders_count
FROM `order` o
WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
GROUP BY month_no,label
ORDER BY month_no
";

    }

}

/*TABLE DATA */

$whereCondition = "";

/* DAILY */
if ($periodFilter == "daily") {
    $whereCondition = "WHERE DATE(o.Order_Date)=CURDATE()";
}

/* WEEKLY */ elseif ($periodFilter == "weekly") {
    $whereCondition = "WHERE YEARWEEK(o.Order_Date,1)=YEARWEEK(CURDATE(),1)";
}

/* MONTHLY */ elseif ($periodFilter == "monthly") {
    $whereCondition = "WHERE MONTH(o.Order_Date)=MONTH(CURDATE()) 
AND YEAR(o.Order_Date)=YEAR(CURDATE())";
}

/* YEARLY */ elseif ($periodFilter == "yearly") {

    if ($monthFilter) {

        $whereCondition = "WHERE YEAR(o.Order_Date)=YEAR(CURDATE())
AND MONTH(o.Order_Date)='$monthFilter'";

    } else {

        $whereCondition = "WHERE YEAR(o.Order_Date)=YEAR(CURDATE())";

    }

}

/* TABLE QUERY */

$tableQuery = "

SELECT 
a.Area_Name,
o.Order_Id,
CONCAT(u.First_Name,' ',u.Last_Name) AS customer,
DATE(o.Order_Date) AS order_date

FROM `order` o
JOIN user_details u ON o.User_Id=u.User_Id
JOIN area_details a ON u.Area_Id=a.Area_Id

$whereCondition

ORDER BY a.Area_Name , o.Order_Date DESC

";

$tableResult = mysqli_query($connection, $tableQuery);

while ($row = mysqli_fetch_assoc($tableResult)) {

    $tableData[] = $row;
    $totalOrders++;

}

/*EXECUTE CHART QUERY */

$result = mysqli_query($connection, $query);

while ($row = mysqli_fetch_assoc($result)) {

    if (isset($row['label'])) {
        $labels[] = $row['label'];
    }

    if (isset($row['orders_count'])) {
        $data[] = $row['orders_count'];
    }

}

/* Prevent empty chart */

if (empty($labels)) {
    $labels[] = "No Data";
    $data[] = 0;
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <title>Area-wise Orders Report</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>



<style>
body{
    font-family:"Segoe UI",Arial,sans-serif;
    background:white;
    margin:0;
    color:#333;
}
.container{
    width:94%;
    margin:15px auto;
}
h1{
    color:#7e2626d5;
    margin-bottom:12px;
    font-size:24px;
    font-weight:600;
    border-left:5px solid #7e2626d5;
    padding-left:8px;
}

/* FILTER ROW */
.filter-row{
    display:flex;
    gap:10px;
    align-items:flex-end;
    flex-wrap:wrap;
    background:white;
    padding:12px 18px;
    border-radius:6px;
    box-shadow:0 2px 15px rgba(0,0,0,0.05);
    margin-bottom:15px;
    border:2px solid #7e2626d5;
}

.filter-row label{
    font-size:18px;
    font-weight:600;
    margin-bottom:3px;
}

.filter-row select,
.filter-row input{
    padding:6px 8px;
    border:1px solid #ddd;
    border-radius:4px;
    font-size:13px;
    min-width:120px;
}

button{
    background:#7e2626d5;
    color:white;
    border:none;
    padding:6px 14px;
    border-radius:4px;
    cursor:pointer;
    font-weight:600;
    font-size:13px;
    transition:0.2s;
}

button:hover{
    background:#5f1d1d;
}

/* PDF BUTTON */
.pdf-btn{
    padding:6px 12px;
    border-radius:4px;
    color:white;
    font-weight:600;
    font-size:13px;
    text-decoration:none;
    background:#c0392b;
}
/* PDF AND EXCEL BUTTONS*/

.pdf-btn,
.excel-btn{
padding:6px 12px;
border-radius:4px;
color:white;
font-weight:600;
font-size:13px;
text-decoration:none;
}

.excel-btn{
background:#27ae60;
}
/* SUMMARY BOX */
.summary{
    display:flex;
    gap:15px;
    margin-bottom:15px;
}

.summary div{
    background:white;
    padding:10px 14px;
    border-radius:6px;
    box-shadow:0 2px 6px rgba(0,0,0,0.06);
    font-size:15px;
    font-weight:600;
    border-left:4px solid #7e2626d5;
}

/* CHART */
.chart-box{
    background:white;
    padding:10px;
    border-radius:6px;
    box-shadow:0 2px 6px rgba(0,0,0,0.06);
    margin-bottom:18px;
    border:2px solid #7e2626d5;
    max-width:800px;
    margin-left:auto;
    margin-right:auto;
    height:350px;
}
.title-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
}

/* BACK BUTTON */
.back-btn{
    text-decoration:none;
    font-size:20px;
    font-weight:600;
    color:#0b6e77;
    padding:6px 12px;
    border-radius:6px;
    transition:0.2s;
}

.back-btn:hover{
    color:#7e2626d5;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border:2px solid #7e2626d5;
}

th{
    background:#7e2626d5;
    color:white;
    padding:8px;
    font-size:13px;
    border:1px solid #ddd;
}

td{
    padding:7px;
    font-size:13px;
    border:1px solid #ddd;
    text-align:center;
}

tr:nth-child(even){
    background:#faf7f6;
}

tr:hover{
    background:#f2e9e8;
}

tfoot td{
    background:#f8eceb;
    font-weight:600;
    border-top:2px solid #7e2626d5;
}

/* AREA HEADER ROW */
.area-header{
    background:#f8f3ee;
    font-weight:bold;
    color:#7e2626d5;
    text-align:left;
    padding-left:10px;
}
</style>

</head>

<body>

    <div class="container">

        <div class="title-row">

            <h1>Area-wise Orders Report</h1>

            <a href="http://localhost/GitHub/gift-shop-website/AdminPanel/layout.php?view=report_layout"
                class="back-btn">
                ← Back
            </a>

        </div>

        <form method="GET">

            <div class="filter-row">

                <label>Period</label>

                <select name="period">

                    <option value="daily" <?= ($periodFilter == 'daily') ? 'selected' : '' ?>>Daily</option>
                    <option value="weekly" <?= ($periodFilter == 'weekly') ? 'selected' : '' ?>>Weekly</option>
                    <option value="monthly" <?= ($periodFilter == 'monthly') ? 'selected' : '' ?>>Monthly</option>
                    <option value="yearly" <?= ($periodFilter == 'yearly') ? 'selected' : '' ?>>Yearly</option>

                </select>

                <?php if ($periodFilter == "yearly") { ?>

                    <label>Month</label>

                    <select name="month">

                        <option value="">Select</option>

                        <?php
                        for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 10));
                            ?>

                            <option value="<?= $m ?>" <?= ($monthFilter == $m) ? 'selected' : '' ?>>
                                <?= $monthName ?>
                            </option>

                        <?php } ?>

                    </select>

                <?php } ?>

                <button type="submit">Filter</button>

                <button type="button" onclick="downloadPDF()" class="pdf-btn">
PDF
</button>

                <a href="export_delivery_area_excel.php?period=<?= $periodFilter ?>&month=<?= $monthFilter ?>"
                    class="excel-btn">
                    Excel
                </a>
            </div>

        </form>
<form id="pdfForm" method="POST" action="export_delivery_area_pdf.php" target="_blank">
    
<input type="hidden" name="period" value="<?= $periodFilter ?>">
<input type="hidden" name="month" value="<?= $monthFilter ?>">
<input type="hidden" name="chart_image" id="chartImage">

</form>

        <div class="summary">

            <div>Total Orders : <?= $totalOrders ?></div>

            <div>Total Areas : <?= count($labels) ?></div>

        </div>

        <div class="chart-box">

            <canvas id="areaChart"></canvas>

        </div>

        <table>

            <thead>

                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Date of Order</th>
                </tr>

            </thead>

            <tbody>

                <?php

                $currentArea = "";

                foreach ($tableData as $row) {

                    if ($currentArea != $row['Area_Name']) {

                        $currentArea = $row['Area_Name'];

                        ?>

                        <tr class="area-header">
                            <td colspan="3">
                                Area : <?= $currentArea ?>
                            </td>
                        </tr>

                    <?php } ?>

                    <tr>

                        <td><?= $row['Order_Id'] ?></td>

                        <td><?= $row['customer'] ?></td>

                        <td><?= $row['order_date'] ?></td>

                    </tr>

                <?php } ?>

            </tbody>

            <tfoot>

                <tr style="font-weight:bold;background:#f1f1f1">

                    <td colspan="2" style="text-align:right">
                        Total Orders
                    </td>

                    <td>
                        <?= $totalOrders ?>
                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

    <script>

        const labels = <?= json_encode($labels) ?>;
        const orders = <?= json_encode($data) ?>;

        const total = orders.reduce((a, b) => Number(a) + Number(b), 0);

        new Chart(document.getElementById("areaChart"), {

            type: 'pie',

            data: {
                labels: labels,
                datasets: [{
                    data: orders,
                    backgroundColor: [
                        '#7e2626d5',
                        '#a94442',
                        '#c97d60',
                        '#d4a373',
                        '#e6ccb2',
                        '#bc4749'
                    ]
                }]
            },

            plugins: [ChartDataLabels],

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        position: 'right'
                    },

                    datalabels: {
                        color: 'white',
                        font: { weight: 'bold', size: 12 },

                        formatter: (value) => {

                            value = Number(value);

                            if (!total || total === 0) {
                                return "0%\n0 Orders";
                            }

                            let percentage = ((value / total) * 100).toFixed(1);

                            if (isNaN(percentage)) {
                                percentage = 0;
                            }

                            return percentage + "%\n" + value + " Orders";

                        }

                    }

                }

            }

        });
function downloadPDF(){

const canvas = document.getElementById("areaChart");

const image = canvas.toDataURL("image/png");

document.getElementById("chartImage").value = image;

document.getElementById("pdfForm").submit();

}

    </script>

</body>

</html>
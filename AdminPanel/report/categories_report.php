<?php
if (!isset($_SESSION))
    session_start();
include(__DIR__ . '/../db.php');
/* ================= CATEGORY WISE REVENUE ================= */
$categoryRevenueQuery = mysqli_query($connection, "
    SELECT 
        c.Category_Name,
        SUM(oi.Quantity * oi.Price_Snapshot) AS total_revenue
    FROM order_item oi
    JOIN product_details p ON oi.Product_Id = p.Product_Id
    JOIN category_details c ON p.Category_Id = c.Category_Id
    GROUP BY c.Category_Id, c.Category_Name
    ORDER BY total_revenue DESC
");

$categoryNames = [];
$categoryRevenue = [];

while ($row = mysqli_fetch_assoc($categoryRevenueQuery)) {
    $categoryNames[] = $row['Category_Name'];
    $categoryRevenue[] = $row['total_revenue'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>

<body>
 <!-- ================= CATEGORY REVENUE PIE CHART ================= -->
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626d5;">
                        Category Wise Revenue Distribution
                    </h2>
                    <a href="dashboard/export_category_revenue_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>
                <div style="width:400px; margin:auto;">
                    <canvas id="categoryPieChart"></canvas>
                </div>
            </div>
            <script>
        const categoryLabels = <?= json_encode($categoryNames); ?>;
        const categoryData = <?= json_encode($categoryRevenue); ?>;

        // Calculate total revenue
        const totalRevenue = categoryData.reduce((a, b) => a + parseFloat(b), 0);

        new Chart(document.getElementById('categoryPieChart'), {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    datalabels: {
                        color: 'white',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function (value) {
                            let percent = ((value / totalRevenue) * 100).toFixed(1);
                            return percent + "%\n₹" + value;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
    </body>
    </html>

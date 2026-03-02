<?php
if (!isset($_SESSION))
    session_start();
include(__DIR__ . '/../db.php');

/* ================= SUMMARY ================= */
$totalUsers = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total FROM user_details WHERE User_Role='CUSTOMER'"
))['total'];

$totalProducts = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total FROM product_details"
))['total'];

$totalOrders = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total FROM `order`"
))['total'];

/* ================= RECENT ORDERS ================= */
$recentOrders = mysqli_query($connection, "
    SELECT o.Order_Id, o.Order_Date, o.Total_Amount,
           u.First_Name, u.Last_Name
    FROM `order` o
    LEFT JOIN user_details u ON o.User_Id = u.User_Id
    ORDER BY o.Order_Date DESC
    LIMIT 5
");

/* ================= FILTER ================= */
$from = $_GET['from_date'] ?? '';
$to = $_GET['to_date'] ?? '';

$where = "";
if ($from && $to) {
    $where = "WHERE DATE(Order_Date) BETWEEN '$from' AND '$to'";
}

/* ================= MONTHLY SALES ================= */
$salesQuery = mysqli_query($connection, "
    SELECT YEAR(Order_Date) as year,
           MONTH(Order_Date) as month,
           SUM(Total_Amount) as total_sales
    FROM `order`
    $where
    GROUP BY YEAR(Order_Date), MONTH(Order_Date)
    ORDER BY YEAR(Order_Date), MONTH(Order_Date)
");

$months = [];
$sales = [];

while ($row = mysqli_fetch_assoc($salesQuery)) {
    $months[] = date("M Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
    $sales[] = $row['total_sales'];
}

$filteredTotal = mysqli_num_rows(mysqli_query(
    $connection,
    "SELECT Order_Id FROM `order` $where"
));

/* ================= TOP SELLING PRODUCTS ================= */
/* ================= TOP SELLING PRODUCTS ================= */
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
    LIMIT 5
");

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

<body style="background:#f4f6f9">
    <div class="container mt-4">

        <h2 class="mb-4">Dashboard Overview</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Total Users</h6>
                    <h3><?= $totalUsers ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Total Products</h6>
                    <h3><?= $totalProducts ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Total Orders</h6>
                    <h3><?= $totalOrders ?></h3>
                </div>
            </div>
        </div>

        <!-- ================= RECENT ORDERS ================= -->
        <div class="mt-5 card p-4">
            <h4>Recent Orders</h4>

            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = mysqli_fetch_assoc($recentOrders)) { ?>
                        <tr>
                            <td><?= $r['Order_Id'] ?></td>
                            <td><?= $r['First_Name'] . ' ' . $r['Last_Name'] ?></td>
                            <td><?= $r['Order_Date'] ?></td>
                            <td>₹<?= number_format($r['Total_Amount'], 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- ================= REPORT SECTION ================= -->
        <div class="mt-5 card p-4 shadow">

            <h1 class="fw-bold text-center mb-4" style="font-size:38px;">
                📊 SALES REPORTS
            </h1>

            <h2 class="fw-bold mb-4 text-primary" style="font-size:28px;">
                📅 Monthly Sales
            </h2>

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="fw-semibold">From Date</label>
                    <input type="date" name="from_date" value="<?= $from ?>" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="fw-semibold">To Date</label>
                    <input type="date" name="to_date" value="<?= $to ?>" class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary me-2">Filter</button>

                    <a href="dashboard/export_monthly_sale_pdf.php?from_date=<?= $from ?>&to_date=<?= $to ?>"
                        target="_blank" class="btn btn-danger">
                        Generate PDF
                    </a>
                </div>
            </form>

            <h5 class="mb-3">
                Total Orders (Filtered):
                <span class="badge bg-success fs-6"><?= $filteredTotal ?></span>
            </h5>

            <canvas id="salesChart" height="100"></canvas>

            <!-- ================= TOP PRODUCTS ================= -->
            <div class="mt-5">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold text-success" style="font-size:26px;">
                        🏆 Top Selling Products
                    </h2>

                    <a href="dashboard/export_top_products_pdf.php" target="_blank" class="btn btn-danger">
                        Generate PDF
                    </a>
                </div>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Product Name</th>
                            <th>Total Quantity Sold</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($topProductsQuery) > 0) { ?>
                            <?php while ($tp = mysqli_fetch_assoc($topProductsQuery)) { ?>
                                <tr>
                                    <td><?= $tp['Product_Name'] ?></td>
                                    <td><?= $tp['total_sold'] ?></td>
                                    <td>₹<?= number_format($tp['total_revenue'], 2) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No product sales data available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
            <!-- ================= CATEGORY REVENUE PIE CHART ================= -->
            <div class="mt-5 text-center">

                <h2 class="fw-bold text-danger mb-4" style="font-size:24px;">
                    🥧 Category Wise Revenue Distribution
                </h2>


                <a href="dashboard/export_category_revenue_pdf.php" target="_blank" class="btn btn-danger">
                    Generate PDF
                </a>
                <div style="width:400px; margin:auto;">
                    <canvas id="categoryPieChart"></canvas>
                </div>

            </div>
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
    <script>
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($months); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?= json_encode($sales); ?>,
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    </script>

</body>

</html>
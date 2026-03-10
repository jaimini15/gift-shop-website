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


/* ================= TOP SELLING PRODUCTS  && LOW SELLING PRODUCTS================= */
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
$lowProductsQuery = mysqli_query($connection, "
    SELECT 
        oi.Product_Id,
        p.Product_Name,
        SUM(oi.Quantity) AS total_sold,
        SUM(oi.Quantity * oi.Price_Snapshot) AS total_revenue
    FROM order_item oi
    JOIN product_details p 
        ON oi.Product_Id = p.Product_Id
    GROUP BY oi.Product_Id, p.Product_Name
    HAVING total_sold > 0
    ORDER BY total_sold ASC
    LIMIT 5
");

/* ================= YEAR WISE REVENUE ================= */
$yearQuery = mysqli_query($connection, "
    SELECT 
        YEAR(Order_Date) AS year,
        SUM(Total_Amount) AS total_revenue,
        COUNT(Order_Id) AS total_orders
    FROM `order`
    WHERE Status='CONFIRM'
    GROUP BY YEAR(Order_Date)
    ORDER BY YEAR(Order_Date)
");

$years = [];
$yearRevenue = [];

while ($row = mysqli_fetch_assoc($yearQuery)) {
    $years[] = $row['year'];
    $yearRevenue[] = $row['total_revenue'];
}
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


/* ================= ORDERS BY AREA ================= */
$areaQuery = "
    SELECT 
        a.Area_Name,
        COUNT(o.Order_Id) AS total_orders
    FROM `order` o
    JOIN user_details u ON o.User_Id = u.User_Id
    JOIN area_details a ON u.Area_Id = a.Area_Id
    GROUP BY a.Area_Name
    ORDER BY total_orders DESC
";

$areaResult = mysqli_query($connection, $areaQuery);

$areaLabels = [];
$areaData = [];

while ($row = mysqli_fetch_assoc($areaResult)) {
    $areaLabels[] = $row['Area_Name'];
    $areaData[] = $row['total_orders'];
}
/* ================= PRODUCTS WITHOUT SALES LOGIC ================= */

$unsoldProductsQuery = mysqli_query($connection, "
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

$unsoldCount = mysqli_num_rows($unsoldProductsQuery);

/* ================= LOW STOCK PRODUCTS ================= */

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

$lowStockCount = mysqli_num_rows($lowStockQuery);

/* ================= DELIVERY STATUS COUNT ================= */

$deliveryStatusQuery = mysqli_query($connection, "
    SELECT 
        Delivery_Status,
        COUNT(*) AS total_count
    FROM delivery_details
    GROUP BY Delivery_Status
");

$statusLabels = [];
$statusData = [];

while ($row = mysqli_fetch_assoc($deliveryStatusQuery)) {
    $statusLabels[] = $row['Delivery_Status'];
    $statusData[] = $row['total_count'];
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
                SALES REPORTS
            </h1>
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                        Monthly Sales
                    </h2>
                    <a href="dashboard/export_monthly_sale_pdf.php?from_date=<?= $from ?>&to_date=<?= $to ?>"
                        target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>
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
                        <button class="btn btn-danger me-2" style="background-color:#7e2626;color:white;">Filter</button>
                    </div>
                </form>

                <h5 class="mb-3">
                    Total Orders (Filtered):
                    <span class="badge bg-success fs-6"><?= $filteredTotal ?></span>
                </h5>

                <canvas id="salesChart" height="175"></canvas>
            </div>
            <!-- ================= TOP & LOW PRODUCTS ================= -->
            <div class="mt-5 card p-4">

                <!-- ================= TOP SELLING ================= -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                         Top Selling Products
                    </h2>
                    <a href="dashboard/export_top_low_selling_products_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>

                <table class="table table-bordered table-striped mb-5">
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


                <!-- ================= LOW SELLING ================= -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                        Low Selling Products
                    </h2>
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
                        <?php if (mysqli_num_rows($lowProductsQuery) > 0) { ?>
                            <?php while ($lp = mysqli_fetch_assoc($lowProductsQuery)) { ?>
                                <tr>
                                    <td><?= $lp['Product_Name'] ?></td>
                                    <td><?= $lp['total_sold'] ?></td>
                                    <td>₹<?= number_format($lp['total_revenue'], 2) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No low selling product data available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
<!--Yearly revenue -->
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                    Year Wise Revenue
                </h2>

                <a href="dashboard/export_yearly_revenue_pdf.php" target="_blank" class="btn btn-primary mb-3">
                    Generate PDF
                </a>
    </div>
                <div style="width:600px; margin:auto;">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
            <!-- ================= CATEGORY REVENUE PIE CHART ================= -->
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
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

            <!-- ================= ORDERS BY DELIVERY AREA PIE CHART ================= -->
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                        Orders by Delivery Area Distribution
                    </h2>
                    <a href="dashboard/export_area_orders_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>
                <div style="width:400px; margin:auto;">
                    <canvas id="areaPieChart"></canvas>
                </div>

            </div>

            <!-- ================= PRODUCTS WITHOUT SALES ================= -->
            <div class="mt-5 card p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                        Products Without Sales
                    </h2>

                    <a href="dashboard/export_products_without_sales_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>

                <h5>
                    Total Unsold Products:
                    <span class="badge bg-danger fs-6">
                        <?= $unsoldCount ?>
                    </span>
                </h5>

                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($unsoldCount > 0) { ?>

                            <?php while ($row = mysqli_fetch_assoc($unsoldProductsQuery)) { ?>
                                <tr>
                                    <td><?= $row['Product_Id'] ?></td>
                                    <td><?= $row['Product_Name'] ?></td>
                                    <td>₹<?= number_format($row['Price'], 2) ?></td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center text-success">
                                    All products have sales
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>

            <!-- ================= LOW STOCK PRODUCTS ================= -->
            <div class="mt-5 card p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold " style="font-size:26px;color:#7e2626;">
                        Low Stock Products (Less Than 5)
                    </h2>

                    <a href="dashboard/export_low_stock_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>

                <h5>
                    Total Low Stock Products:
                    <span class="badge bg-warning text-dark fs-6">
                        <?= $lowStockCount ?>
                    </span>
                </h5>

                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock Available</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($lowStockCount > 0) { ?>

                            <?php while ($row = mysqli_fetch_assoc($lowStockQuery)) { ?>
                                <tr>
                                    <td><?= $row['Product_Id'] ?></td>
                                    <td><?= $row['Product_Name'] ?></td>
                                    <td>₹<?= number_format($row['Price'], 2) ?></td>
                                    <td class="fw-bold " style="color:#7e2626;">
                                        <?= $row['Stock_Available'] ?>
                                    </td>
                                    <td><?= $row['Last_Update'] ?></td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="5" class="text-center text-success">
                                    All products have sufficient stock 
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>

            <!-- ================= DELIVERY STATUS PIE CHART ================= -->
            <div class="mt-5 card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold" style="font-size:26px;color:#7e2626;">
                        Order Delivery Status Distribution
                    </h2>
                    <a href="dashboard/export_delivery_status_pdf.php" target="_blank" class="btn btn-primary">
                        Generate PDF
                    </a>
                </div>
                <div style="width:400px; margin:auto;">
                    <canvas id="deliveryStatusPieChart"></canvas>
                </div>
            </div>
        </div>

    </div>
    <script>
        new Chart(document.getElementById('yearChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($years); ?>,
                datasets: [{
                    label: 'Year Wise Revenue',
                    data: <?= json_encode($yearRevenue); ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5000
                        }
                    }
                }
            }
        });
    </script>
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
    <script>
        const areaLabels = <?= json_encode($areaLabels); ?>;
        const areaData = <?= json_encode($areaData); ?>;

        // Calculate total orders
        const totalAreaOrders = areaData.reduce((a, b) => a + parseInt(b), 0);

        new Chart(document.getElementById('areaPieChart'), {
            type: 'pie',
            data: {
                labels: areaLabels,
                datasets: [{
                    data: areaData
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
                            let percent = ((value / totalAreaOrders) * 100).toFixed(1);
                            return percent + "%\n" + value + " Orders";
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>

    <script>
        const deliveryLabels = <?= json_encode($statusLabels); ?>;
        const deliveryData = <?= json_encode($statusData); ?>;

        const totalDelivery = deliveryData.reduce((a, b) => a + parseInt(b), 0);

        new Chart(document.getElementById('deliveryStatusPieChart'), {
            type: 'pie',
            data: {
                labels: deliveryLabels,
                datasets: [{
                    data: deliveryData,
                    backgroundColor: [
                        '#ffc107',   // Packed (yellow)
                        '#17a2b8',   // Out for Delivery (blue)
                        '#28a745'    // Delivered (green)
                    ]
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
                            let percent = ((value / totalDelivery) * 100).toFixed(1);
                            return percent + "%\n" + value;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>

</body>

</html>
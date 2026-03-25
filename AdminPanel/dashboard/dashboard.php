<?php
if (!isset($_SESSION))
    session_start();
include(__DIR__ . '/../db.php');

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
    "SELECT COUNT(*) AS total FROM `order` WHERE DATE(Order_Date) = CURDATE()"
))['total'];

$recentOrders = mysqli_query($connection, "
    SELECT o.Order_Id, o.Order_Date, o.Total_Amount,
           u.First_Name, u.Last_Name
    FROM `order` o
    LEFT JOIN user_details u ON o.User_Id = u.User_Id
    ORDER BY o.Order_Date DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        .card-box {
            background: #fff;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h6 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .card h3 {
            font-size: 24px;
            font-weight: bold;
        }

        /* table styling */
        table {
            width: 100%;
            font-size: 13px;
        }

        .table th,
        .table td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        /* table header */
        .table thead th {
            font-size: 13px;
            font-weight: 600;
        }

        /* ID column */
        th:first-child,
        td:first-child {
            text-align: center;
            white-space: nowrap;
        }

        /* date column */
        th:nth-child(3),
        td:nth-child(3) {
            white-space: nowrap;
        }

        /* amount column */
        th:last-child,
        td:last-child {
            white-space: nowrap;
            text-align: right;
        }

        /* hover effect */
        .table tbody tr:hover {
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="card-box">

        <h2 style="font-size:26px;font-weight:bold;margin-bottom:25px;">Dashboard Overview</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Total Customer</h6>
                    <h3><?= $totalUsers ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Total Product</h6>
                    <h3><?= $totalProducts ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h6>Today's Order</h6>
                    <h3><?= $totalOrders ?></h3>
                </div>
            </div>
        </div>

        <!--  RECENT ORDERS -->
        <div class="mt-5 card p-4">
            <h4 style="font-size:20px">Recent Order</h4>

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

    </div>
    </div>
</body>

</html>
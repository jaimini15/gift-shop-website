<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . '/../../AdminPanel/db.php');

if (!isset($_SESSION['User_Id'])) {
    echo "<div class='alert alert-danger m-3'>Unauthorized access</div>";
    exit;
}

$deliveryBoyId = (int) $_SESSION['User_Id'];


// Assigned Orders
$assigned = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(DISTINCT d.Order_Id) AS total
    FROM delivery_details d
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    WHERE m.delivery_boy_id = $deliveryBoyId
    AND d.Delivery_Status IN ('Packed', 'Out for Delivery')
"))['total'] ?? 0;

// Completed Orders
$completed = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(DISTINCT d.Order_Id) AS total
    FROM delivery_details d
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    WHERE m.delivery_boy_id = $deliveryBoyId
    AND d.Delivery_Status = 'Delivered'
"))['total'] ?? 0;

// Today's Deliveries
$today = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(*) AS total
    FROM delivery_details d
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    WHERE m.delivery_boy_id = $deliveryBoyId
    AND DATE(d.Delivery_Date) = CURDATE()
"))['total'] ?? 0;

/* RECENT ORDERS */
$recentOrders = mysqli_query($connection, "
    SELECT 
        o.Order_Id,
        CONCAT(u.First_Name,' ',u.Last_Name) AS Customer,
        a.Area_Name,
        o.Total_Amount,
        d.Delivery_Status
    FROM delivery_details d
    JOIN `order` o ON o.Order_Id = d.Order_Id
    JOIN user_details u ON u.User_Id = o.User_Id
    JOIN area_details a ON a.Area_Id = d.Area_Id
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    WHERE m.delivery_boy_id = $deliveryBoyId
    ORDER BY o.Order_Date DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Delivery Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

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

        /* TABLE  */
        table {
            width: 100%;
            font-size: 13px;
        }

        .table th,
        .table td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 13px;
            font-weight: 600;
        }

        th:first-child,
        td:first-child {
            text-align: center;
            white-space: nowrap;
        }

        th:last-child,
        td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="card-box">

    <h2 style="font-size:26px;font-weight:bold;margin-bottom:25px;">
        Dashboard Overview
    </h2>
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h6>Assigned Orders</h6>
                <h3><?= $assigned ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h6>Completed Deliveries</h6>
                <h3><?= $completed ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h6>Today’s Deliveries</h6>
                <h3><?= $today ?></h3>
            </div>
        </div>

    </div>

    <!--  RECENT ORDERS -->
    <div class="mt-5 card p-4">

        <h4 style="font-size:20px">Recent Orders</h4>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Area</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php if (mysqli_num_rows($recentOrders) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No recent orders
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while ($row = mysqli_fetch_assoc($recentOrders)): ?>

                    <tr>
                        <td><?= $row['Order_Id'] ?></td>
                        <td><?= htmlspecialchars($row['Customer']) ?></td>
                        <td><?= htmlspecialchars($row['Area_Name']) ?></td>
                        <td>₹<?= number_format($row['Total_Amount'], 2) ?></td>
                        <td>
                            <?php
                            $statusClass = '';

                            if ($row['Delivery_Status'] === 'Delivered') {
                                $statusClass = 'bg-success';
                            } elseif ($row['Delivery_Status'] === 'Packed') {
                                $statusClass = 'bg-warning text-dark';
                            } elseif ($row['Delivery_Status'] === 'Out for Delivery') {
                                $statusClass = 'bg-primary';
                            } else {
                                $statusClass = 'bg-secondary';
                            }
                            ?>

                            <span class="badge <?= $statusClass ?>">
                                <?= $row['Delivery_Status'] ?>
                            </span>
                        </td>
                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
<?php
/* ================= SESSION ================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= DB CONNECTION ================= */
include(__DIR__ . '/../../AdminPanel/db.php');

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['User_Id'])) {
    echo "<div class='alert alert-danger m-3'>Unauthorized access</div>";
    exit;
}

$deliveryBoyId = (int) $_SESSION['User_Id'];


// Assigned Orders (Packed + Out for Delivery)
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

// Today Deliveries
$today = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(*) AS total
    FROM delivery_details d
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    WHERE m.delivery_boy_id = $deliveryBoyId
    AND DATE(d.Delivery_Date) = CURDATE()
"))['total'] ?? 0;

/* ================= RECENT ORDERS ================= */
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

<!-- ================= DASHBOARD ================= -->

<h3 class="fw-bold mb-4">
    <i class="fa-solid fa-chart-line"></i> Dashboard
</h3>

<div class="row mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Assigned Orders</h6>
                <h2 class="fw-bold"><?= $assigned ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Completed Deliveries</h6>
                <h2 class="fw-bold"><?= $completed ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Today’s Deliveries</h6>
                <h2 class="fw-bold"><?= $today ?></h2>
            </div>
        </div>
    </div>

</div>

<!-- ================= RECENT ORDERS ================= -->

<div class="card shadow-sm">
    <div class="card-body">

        <h5 class="fw-bold mb-3">Recent Orders</h5>

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
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
                        <span class="badge 
                            <?= $row['Delivery_Status'] === 'Delivered' ? 'bg-success' : 'bg-warning' ?>">
                            <?= $row['Delivery_Status'] ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>

    </div>
</div>

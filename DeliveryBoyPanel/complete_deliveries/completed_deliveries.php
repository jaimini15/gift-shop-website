<?php
if (!isset($_SESSION)) {
    session_start();
}

include("../AdminPanel/db.php");

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['delivery_id'])) {
    header("Location: login.php");
    exit;
}
$deliveryBoyId = (int) $_SESSION['delivery_id'];
/* ============================================== */


/* ========== FETCH ALL COMPLETED DELIVERIES ========= */
$orders = mysqli_query($connection, "
    SELECT DISTINCT
        o.Order_Id,
        DATE(o.Order_Date) AS Order_Date,
        o.Total_Amount,
        d.Delivery_Status,
        d.Delivery_Date,
        a.Area_Name,
        u.Phone,
        u.Address,
        CONCAT(u.First_Name, ' ', u.Last_Name) AS Customer_Name
    FROM delivery_details d
    JOIN `order` o ON o.Order_Id = d.Order_Id
    JOIN user_details u ON u.User_Id = o.User_Id
    JOIN delivery_area_map m ON m.area_id = d.Area_Id
    JOIN area_details a ON a.Area_Id = d.Area_Id
    WHERE 
        m.delivery_boy_id = $deliveryBoyId
        AND d.Delivery_Status = 'Delivered'
        AND DATE(d.Delivery_Date) <= CURDATE()
    ORDER BY d.Delivery_Date DESC
");
/* ================================================= */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Completed Deliveries</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:12px; }
.date-row {
    background:#e9ecef;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container mt-4">
    <h3 class="mb-4 fw-bold">Completed Deliveries</h3>

    <div class="card p-3">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Area</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Delivered On</th>
                </tr>
            </thead>
            <tbody>

            <?php
            if (mysqli_num_rows($orders) == 0) {
                echo '
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No completed deliveries
                    </td>
                </tr>';
            }

            $lastDate = null;

            while ($row = mysqli_fetch_assoc($orders)) {

                $currentDate = date("Y-m-d", strtotime($row['Delivery_Date']));

                if ($lastDate !== $currentDate) {
                    echo '
                    <tr class="date-row">
                        <td colspan="9">
                            📅 ' . date("d-m-Y", strtotime($currentDate)) . '
                        </td>
                    </tr>';
                    $lastDate = $currentDate;
                }
            ?>
                <tr>
                    <td><?= $row['Order_Id'] ?></td>
                    <td><?= htmlspecialchars($row['Customer_Name']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td><?= htmlspecialchars($row['Area_Name']) ?></td>
                    <td><?= date("d-m-Y", strtotime($row['Order_Date'])) ?></td>
                    <td>₹<?= number_format($row['Total_Amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['Phone']) ?></td>
                    <td>
                        <span class="badge bg-success">
                            <?= $row['Delivery_Status'] ?>
                        </span>
                    </td>
                    <td><?= date("d-m-Y h:i A", strtotime($row['Delivery_Date'])) ?></td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
</div>

</body>
</html>

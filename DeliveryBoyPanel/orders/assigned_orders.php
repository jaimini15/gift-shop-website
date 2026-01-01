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


/* ========== UPDATE DELIVERY STATUS ============= */
if (isset($_POST['order_id'], $_POST['delivery_status'])) {

    $orderId = (int) $_POST['order_id'];
    $status  = $_POST['delivery_status'];

    if ($status === 'Out of Delivery') {

        mysqli_query($connection, "
            UPDATE delivery_details 
            SET Delivery_Status = 'Out of Delivery'
            WHERE Order_Id = $orderId
        ");

    } elseif ($status === 'Delivered') {

        mysqli_query($connection, "
            UPDATE delivery_details 
            SET Delivery_Status = 'Delivered',
                Delivery_Date = NOW()
            WHERE Order_Id = $orderId
        ");
    }
}
/* ============================================== */


/* ========== FETCH ASSIGNED ORDERS ============== */
$orders = mysqli_query($connection, "
    SELECT 
        o.Order_Id,
        DATE(o.Order_Date) AS Order_Date,
        o.Total_Amount,
        d.Delivery_Status,
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
        AND m.status = 'ACTIVE'
        AND d.Delivery_Status IN ('Packed', 'Out of Delivery')
    ORDER BY o.Order_Date DESC
");
/* ============================================== */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assigned Orders</title>

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
    <h3 class="mb-4 fw-bold">Assigned Orders</h3>

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
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>

            <?php
            if (mysqli_num_rows($orders) == 0) {
                echo '
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No assigned orders
                    </td>
                </tr>';
            }

            $lastDate = null;

            while ($row = mysqli_fetch_assoc($orders)) {

                if ($lastDate !== $row['Order_Date']) {
                    echo '
                    <tr class="date-row">
                        <td colspan="9">
                            📅 ' . date("d-m-Y", strtotime($row['Order_Date'])) . '
                        </td>
                    </tr>';
                    $lastDate = $row['Order_Date'];
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
                        <span class="badge bg-primary">
                            <?= $row['Delivery_Status'] ?>
                        </span>
                    </td>

                    <td>
                        <form method="post">
                            <input type="hidden" name="order_id" value="<?= $row['Order_Id'] ?>">
                            <select name="delivery_status"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">

                                <option value="">Select</option>

                                <option value="Out of Delivery"
                                    <?= $row['Delivery_Status'] == 'Out of Delivery' ? 'selected' : '' ?>>
                                    Out of Delivery
                                </option>

                                <option value="Delivered">
                                    Delivered
                                </option>

                            </select>
                        </form>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
</div>

</body>
</html>

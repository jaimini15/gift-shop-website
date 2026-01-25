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

/* ========== FETCH COMPLETED DELIVERIES ========= */
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
?>

<!-- ================= PAGE CONTENT ================= -->

<div class="container-fluid mt-4">

    <h3 class="fw-bold mb-4">Completed Deliveries</h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
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
                if (mysqli_num_rows($orders) === 0):
                ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No completed deliveries found
                        </td>
                    </tr>
                <?php
                endif;

                $lastDate = null;

                while ($row = mysqli_fetch_assoc($orders)):
                    $currentDate = date('Y-m-d', strtotime($row['Delivery_Date']));

                    if ($lastDate !== $currentDate):
                        $lastDate = $currentDate;
                ?>
                        <tr class="table-secondary fw-bold">
                            <td colspan="9">
                                📅 <?= date('d-m-Y', strtotime($currentDate)) ?>
                            </td>
                        </tr>
                <?php endif; ?>

                    <tr>
                        <td><?= $row['Order_Id'] ?></td>
                        <td><?= htmlspecialchars($row['Customer_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Address']) ?></td>
                        <td><?= htmlspecialchars($row['Area_Name']) ?></td>
                        <td><?= date('d-m-Y', strtotime($row['Order_Date'])) ?></td>
                        <td>₹<?= number_format($row['Total_Amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['Phone']) ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?= $row['Delivery_Status'] ?>
                            </span>
                        </td>
                        <td><?= date('d-m-Y h:i A', strtotime($row['Delivery_Date'])) ?></td>
                    </tr>

                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- ================= PAGE CSS ================= -->
<style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .content { margin-left: 0px; padding: 0px;  }
        .card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
</style>

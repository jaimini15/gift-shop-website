<?php
if (!isset($_SESSION)) session_start();

include(__DIR__ . '/../db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivered Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

   <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .content { margin-left: 0px; padding: 0px;  }
        .card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        
    </style>
</head>

<body>

<div class="content">
<div class="card-box">

<h3 class="fw-bold mb-4">Delivered Orders </h3>

<?php
$deliveries = mysqli_query($connection, "
    SELECT 
        o.Order_Id,
        DATE(o.Order_Date) AS Order_Date,
        o.Total_Amount,

        d.Delivery_Status,
        d.Delivery_Date,
        d.Delivery_Address,

        CONCAT(u.First_Name, ' ', u.Last_Name) AS Customer_Name,
        u.Phone,

        a.Area_Name

    FROM delivery_details d
    JOIN `order` o ON o.Order_Id = d.Order_Id
    JOIN user_details u ON u.User_Id = o.User_Id
    LEFT JOIN area_details a ON a.Area_Id = d.Area_Id

    WHERE d.Delivery_Status IN ('Packed','Out of Delivery','Delivered')
    ORDER BY o.Order_Date DESC
");
?>

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
if (mysqli_num_rows($deliveries) == 0) {
    echo '<tr><td colspan="9" class="text-center text-muted">No orders found</td></tr>';
}

$lastDate = null;

while ($row = mysqli_fetch_assoc($deliveries)) {

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
            <td><?= htmlspecialchars($row['Delivery_Address']) ?></td>
            <td><?= htmlspecialchars($row['Area_Name'] ?? 'N/A') ?></td>
            <td><?= date("d-m-Y", strtotime($row['Order_Date'])) ?></td>
            <td>₹<?= number_format($row['Total_Amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['Phone']) ?></td>
            <td>
                <?php
                if ($row['Delivery_Status'] === 'Packed') {
                    echo '<span class="badge bg-warning text-dark">Packed</span>';
                } elseif ($row['Delivery_Status'] === 'Out of Delivery') {
                    echo '<span class="badge bg-primary">Out of Delivery</span>';
                } else {
                    echo '<span class="badge bg-success">Delivered</span>';
                }
                ?>
            </td>
            <td>
                <?= $row['Delivery_Date']
                    ? date("d-m-Y h:i A", strtotime($row['Delivery_Date']))
                    : '-' ?>
            </td>
        </tr>
<?php } ?>

    </tbody>
</table>

</div>
</div>

</body>
</html>

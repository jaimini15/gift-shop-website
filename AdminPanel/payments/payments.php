<?php
if (!isset($_SESSION)) {
    session_start();
}

include(__DIR__ . '/../db.php');

/* ================= AUTH CHECK ================= */
/* Add admin / delivery auth here if needed */
/* ============================================== */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payments</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; font-family: Arial; }
.content { margin-left:120px; padding:20px; margin-top:30px; }
.card-box { background:#fff; padding:20px; border-radius:12px; }
.date-row {
    background:#e9ecef;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="content">
<div class="card-box">

<h3 class="fw-bold mb-4">Payment Details</h3>

<?php
$payments = mysqli_query($connection, "
    SELECT
        p.Payment_Id,
        p.Order_Id,
        p.Payment_Date,
        p.Payment_Method,
        p.Amount,
        p.Payment_Status,
        p.Transaction_Reference,

        DATE(o.Order_Date) AS Order_Date,

        CONCAT(u.First_Name, ' ', u.Last_Name) AS Customer_Name,
        u.Phone

    FROM payment_details p
    JOIN `order` o ON o.Order_Id = p.Order_Id
    JOIN user_details u ON u.User_Id = o.User_Id

    ORDER BY p.Payment_Date DESC, p.Payment_Id DESC
");
?>

<table class="table table-bordered align-middle">
<thead class="table-dark">
<tr>
    <th>Payment ID</th>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Order Date</th>
    <th>Payment Date</th>
    <th>Method</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Transaction Ref</th>
</tr>
</thead>

<tbody>

<?php
if (mysqli_num_rows($payments) == 0) {
    echo '
    <tr>
        <td colspan="10" class="text-center text-muted">
            No payment records found
        </td>
    </tr>';
}

$lastDate = null;

while ($row = mysqli_fetch_assoc($payments)) {

    $currentDate = date("Y-m-d", strtotime($row['Payment_Date']));

    if ($lastDate !== $currentDate) {
        echo '
        <tr class="date-row">
            <td colspan="10">
                📅 ' . date("d-m-Y", strtotime($currentDate)) . '
            </td>
        </tr>';
        $lastDate = $currentDate;
    }
?>
<tr>
    <td><?= $row['Payment_Id'] ?></td>
    <td><?= $row['Order_Id'] ?></td>
    <td><?= htmlspecialchars($row['Customer_Name']) ?></td>
    <td><?= htmlspecialchars($row['Phone']) ?></td>
    <td><?= date("d-m-Y", strtotime($row['Order_Date'])) ?></td>
    <td><?= date("d-m-Y", strtotime($row['Payment_Date'])) ?></td>
    <td><?= htmlspecialchars($row['Payment_Method']) ?></td>
    <td>₹<?= number_format($row['Amount'], 2) ?></td>

    <td>
        <?php
        $status = strtoupper(trim($row['Payment_Status']));

        if ($status === 'SUCCESS') {
            echo '<span class="badge bg-success">Success</span>';
        } elseif ($status === 'PENDING') {
            echo '<span class="badge bg-warning text-dark">Pending</span>';
        } else {
            echo '<span class="badge bg-danger">Failed</span>';
        }
        ?>
    </td>

    <td style="max-width:220px; word-break:break-all;">
        <?= htmlspecialchars($row['Transaction_Reference'] ?? '-') ?>
    </td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</body>
</html>

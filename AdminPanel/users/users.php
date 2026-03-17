<?php
if (!isset($_SESSION)) {
    session_start();
}

include(__DIR__ . '/../db.php');

// FETCH CUSTOMERS WITH AREA & PINCODE 
$query = "
    SELECT 
        u.*,
        a.Area_Name,
        a.Pincode
    FROM user_details u
    LEFT JOIN area_details a 
        ON u.Area_Id = a.Area_Id
    WHERE u.User_Role = 'CUSTOMER'
    ORDER BY u.User_Id DESC
";

$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Customers - Admin Panel</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
body {
    background: #ffffff;
    font-family: Arial, sans-serif;
}

/* card */
.card-box {
    background: #fff;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* table */
.table th,
.table td {
    padding: 6px 8px;
    vertical-align: middle;
    font-size: 12px;
}

/* header */
.table thead th {
    font-size: 13px;
    font-weight: 600;
}

/* ID */
th:first-child,
td:first-child {
    text-align: center;
    white-space: nowrap;
}

/* Email column */
td:nth-child(3) {
    word-break: break-word;
}

/* Phone */
td:nth-child(4) {
    white-space: nowrap;
}

/* Address */
td:nth-child(5) {
    max-width: 200px;
}

/* DOB */
td:nth-child(6) {
    white-space: nowrap;
}

/* Area */
td:nth-child(7) {
    max-width: 200px;
}

/* Status */
td:nth-child(8) {
    text-align: center;
    white-space: nowrap;
}

/* Created At */
td:nth-child(9) {
    text-align: center;
    line-height: 1.2;
}

/* hover */
.table tbody tr:hover {
    background: #f8f9fa;
}
</style>
</head>

<body>

<div class="card-box">

<h2 style="font-size:26px;font-weight:bold;margin-bottom:25px;">
    Customers
</h2>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Address</th>
    <th>DOB</th>
    <th>Area & Pincode</th>
    <th>Status</th>
    <th>Created At</th>
</tr>
</thead>

<tbody>

<?php if (mysqli_num_rows($result) == 0): ?>
<tr>
    <td colspan="9" class="text-center text-muted">
        No customers found
    </td>
</tr>
<?php endif; ?>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['User_Id'] ?></td>

    <td>
        <?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?>
    </td>

    <!-- Email with clean break after @ -->
    <td>
        <?= str_replace('@', '@&#8203;', htmlspecialchars($row['Email'])) ?>
    </td>

    <td><?= htmlspecialchars($row['Phone']) ?></td>

    <td><?= htmlspecialchars($row['Address']) ?></td>

    <td><?= htmlspecialchars($row['DOB']) ?></td>

    <!-- Area + Pincode -->
    <td>
        <?= !empty($row['Area_Name']) 
            ? htmlspecialchars($row['Area_Name'] . ', ' . $row['Pincode']) 
            : 'N/A' ?>
    </td>

    <td><?= htmlspecialchars($row['Status']) ?></td>

    <!-- Created At in 2 lines -->
   <td>
    <?= !empty($row['Create_At'])
        ? date("d-m-Y", strtotime($row['Create_At'])) . "<br>" . date("H:i", strtotime($row['Create_At']))
        : 'N/A' ?>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>

</body>
</html>
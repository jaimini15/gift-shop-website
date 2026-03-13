<?php
if (!isset($_SESSION)) session_start();

include(__DIR__ . '/../db.php');

//FETCH CUSTOMERS WITH AREA & PINCODE 
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
        body { background: #ffffff; font-family: Arial, sans-serif; }
        /* card container */
.card-box{
    background:#fff;
    padding:20px 25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    width:100%;
}

/* table layout */
table{
    width:100%;
    font-size:12px;
}

/* table cells */
.table th,
.table td{
    padding:6px 8px;
    vertical-align:middle;
    overflow-wrap:anywhere;
    hyphens:auto;
}

/* table header */
.table thead th{
    font-size:13px;
    font-weight:600;
}

/* ID column */
th:first-child,
td:first-child{
    white-space:nowrap;
    text-align:center;
}

/* Email column */
th:nth-child(3),
td:nth-child(3){
    word-break:normal;
    max-width:180px;
}

/* Phone column */
th:nth-child(4),
td:nth-child(4){
    white-space:nowrap;
}

/* Address column */
td:nth-child(5){
    max-width:200px;
}

/* DOB column */
th:nth-child(6),
td:nth-child(6){
    white-space:nowrap;
}

/* Area column */
td:nth-child(7){
    max-width:200px;
}

/* Status column */
th:nth-child(8),
td:nth-child(8){
    white-space:nowrap;
    min-width:90px;
    text-align:center;
}

/* Created At column */
th:nth-child(9),
td:nth-child(9){
    white-space:nowrap;
}

/* hover effect (admin dashboard style) */
.table tbody tr:hover{
    background:#f8f9fa;
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

        <?php if (mysqli_num_rows($result) == 0) { ?>
            <tr>
                <td colspan="10" class="text-center text-muted">
                    No customers found
                </td>
            </tr>
        <?php } ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['User_Id'] ?></td>
                <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
                <td><?= $row['Email'] ?></td>
                <td><?= $row['Phone'] ?></td>
                <td><?= $row['Address'] ?></td>
                <td><?= $row['DOB'] ?></td>

                <!--  Area + Pincode logic  -->
                <td>
                    <?php
                    if (!empty($row['Area_Name'])) {
                        echo $row['Area_Name'] . ', ' . $row['Pincode'];
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>

                <td><?= $row['Status'] ?></td>

                <td>
                    <?= !empty($row['Create_At'])
                        ? date("d-m-Y H:i", strtotime($row['Create_At']))
                        : 'N/A' ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>

</div>

</body>
</html>

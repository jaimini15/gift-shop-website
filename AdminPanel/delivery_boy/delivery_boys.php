<?php
include(__DIR__ . '/../db.php');

$query = "
    SELECT 
        u.*, 
        a.Area_Name, 
        a.Pincode,
        GROUP_CONCAT(CONCAT(ad.Area_Name, ' (', ad.Pincode, ')') SEPARATOR '<br>') AS Assigned_Areas
    FROM user_details u
    LEFT JOIN area_details a 
        ON u.Area_Id = a.Area_Id
    LEFT JOIN delivery_area_map dam 
        ON u.User_Id = dam.delivery_boy_id
    LEFT JOIN area_details ad 
        ON dam.area_id = ad.Area_Id
    WHERE u.User_Role='DELIVERY_BOY'
    GROUP BY u.User_Id
";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Boys - Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

   <style>

 body { background: #ffffff; font-family: Arial, sans-serif; }

/* Card container */
.card-box{
    background:#fff;
    padding:20px 25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    width:100%;
}

/* Table layout */
table{
    width:100%;
    font-size:12px;
}

/* Table cells */
.table th,
.table td{
    padding:6px 8px;
    vertical-align:middle;
    overflow-wrap:anywhere;
    hyphens:auto;
}

/* Table header */
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

/* Phone column */
th:nth-child(4),
td:nth-child(4){
    white-space:nowrap;
}

/* Status column */
th:nth-child(9),
td:nth-child(9){
    white-space:nowrap;
    min-width:90px;
    text-align:center;
}

/* Action column */
th:last-child,
td:last-child{
    white-space:nowrap;
    text-align:center;
}

/* Control wide columns */
td:nth-child(3){max-width:180px;} /* Email */
td:nth-child(5){max-width:200px;} /* Address */
td:nth-child(8){max-width:200px;} /* Assigned Areas */

/* Email break control */
th:nth-child(3),
td:nth-child(3){
    word-break:normal;
}

/* Small buttons */
.btn-sm{
    padding:3px 8px;
    font-size:11px;
}

</style>
</head>

<body>

<div class="card-box">

    <h2 style="font-size:26px;font-weight:bold;margin-bottom:25px;">
        <i class="fa-solid fa-motorcycle"></i> Delivery Boys
    </h2>

    <a href="delivery_boy/add_delivery_boy.php" class="btn btn-primary mb-3">
        + Add Delivery Boy
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>DOB</th>
                <th>Area</th>
                <th>Assigned Areas</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['User_Id'] ?></td>
                <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
                <td><?= str_replace('@', '@<wbr>', $row['Email']) ?></td>
                <td><?= $row['Phone'] ?></td>
                <td><?= $row['Address'] ?></td>
                <td><?= $row['DOB'] ?></td>

                <!-- EXISTING MAIN AREA -->
                <td>
                    <?= $row['Area_Name'] ? $row['Area_Name'] . ', ' . $row['Pincode'] : 'N/A' ?>
                </td>

                <!-- ASSIGNED AREAS COLUMN -->
                <td>
                    <?= $row['Assigned_Areas'] ?: 'N/A' ?>
                </td>

                <td><?= $row['Status'] ?></td>
                <td><?= isset($row['Create_At']) ? date("d-m-Y H:i", strtotime($row['Create_At'])) : 'N/A' ?></td>
                <td>
                    <a href="delivery_boy/edit_delivery_boy.php?id=<?= $row['User_Id'] ?>"
                       class="btn btn-warning btn-sm">Edit</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>

</div>

</body>
</html>


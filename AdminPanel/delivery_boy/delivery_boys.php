<?php
include(__DIR__ . '/../db.php');

$query = "SELECT * FROM user_details WHERE User_Role='DELIVERY_BOY'";
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
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .card-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: #343a40 !important;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="card-box">

        <h2 class="fw-bold mb-3">
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
                    <th>Pincode</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['User_Id'] ?></td>
                        <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
                        <td><?= $row['Email'] ?></td>
                        <td><?= $row['Phone'] ?></td>
                        <td><?= $row['Address'] ?></td>
                        <td><?= $row['DOB'] ?></td>
                        <td><?= $row['Pincode'] ?></td>
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
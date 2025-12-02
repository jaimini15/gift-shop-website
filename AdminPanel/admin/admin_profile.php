<?php
include("../session_protect.php");
include("../../AdminPanel/db.php");

$admin_id = $_SESSION['User_Id'];

$q = "SELECT * FROM user_details WHERE User_Id='$admin_id' LIMIT 1";
$admin = mysqli_fetch_assoc(mysqli_query($connection, $q));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-dark text-white text-center">
            <h3>Admin Details</h3>
        </div>

        <div class="card-body">

            <div class="text-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                     width="120" class="rounded-circle shadow">
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>Full Name</th>
                    <td><?= $admin['First_Name'] . " " . $admin['Last_Name']; ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?= $admin['Email']; ?></td>
                </tr>

                <tr>
                    <th>Phone</th>
                    <td><?= $admin['Phone']; ?></td>
                </tr>

                <tr>
                    <th>DOB</th>
                    <td><?= $admin['DOB']; ?></td>
                </tr>

                <tr>
                    <th>Address</th>
                    <td><?= $admin['Address']; ?></td>
                </tr>

                <tr>
                    <th>Pincode</th>
                    <td><?= $admin['Pincode']; ?></td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td><?= $admin['User_Role']; ?></td>
                </tr>

                <tr>
                    <th>Created At</th>
                    <td><?= $admin['Create_At']; ?></td>
                </tr>
            </table>

        </div>
    </div>

</div>

</body>
</html>

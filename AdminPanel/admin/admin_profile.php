<?php
if (!isset($_SESSION)) session_start();

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

include(__DIR__ . '/../db.php');

// Get admin record from user_details table
$admin_id = $_SESSION['admin_id'];

$query = "SELECT * FROM user_details WHERE User_Id = $admin_id LIMIT 1";
$result = mysqli_query($connection, $query);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Profile</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
.card {
    max-width: 650px;
    margin: auto;
    border-radius: 10px;
}
</style>
</head>
<body>

<div class="container mt-4">

<div class="card shadow p-4">
    <h3 class="text-center mb-3">
        <i class="fa-solid fa-id-badge"></i> Admin Profile
    </h3>

    <table class="table table-bordered">

        <tr>
            <th>User ID</th>
            <td><?php echo $admin['User_Id']; ?></td>
        </tr>

        <tr>
            <th>First Name</th>
            <td><?php echo $admin['First_Name']; ?></td>
        </tr>

        <tr>
            <th>Last Name</th>
            <td><?php echo $admin['Last_Name']; ?></td>
        </tr>

        <tr>
            <th>DOB</th>
            <td><?php echo $admin['DOB']; ?></td>
        </tr>

        <tr>
            <th>User Role</th>
            <td><?php echo $admin['User_Role']; ?></td>
        </tr>

        <tr>
            <th>Phone</th>
            <td><?php echo $admin['Phone']; ?></td>
        </tr>

        <tr>
            <th>Address</th>
            <td><?php echo $admin['Address']; ?></td>
        </tr>

        <tr>
            <th>Pincode</th>
            <td><?php echo $admin['Pincode']; ?></td>
        </tr>

        <tr>
            <th>Email</th>
            <td><?php echo $admin['Email']; ?></td>
        </tr>

        <tr>
            <th>Password</th>
            <td><?php echo $admin['Password']; ?></td>
        </tr>

        <tr>
            <th>Created At</th>
            <td><?php echo $admin['Create_At']; ?></td>
        </tr>

    </table>
    <a href="admin/edit_admin_profile.php" class="btn btn-warning mb-2">Edit Profile</a>

</div>

</div>

</body>
</html>

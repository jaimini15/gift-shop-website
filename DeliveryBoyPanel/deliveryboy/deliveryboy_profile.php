<?php
if (!isset($_SESSION)) session_start();

// Only Delivery Boy allowed
if (!isset($_SESSION['delivery_id']) || $_SESSION['delivery_role'] !== "DELIVERY_BOY") {
    header("Location: ../deliveryboy_login/login.php?error=Please login first");
    exit;
}

include(__DIR__ . '/../../AdminPanel/db.php');

$delivery_id = $_SESSION['delivery_id'];

$delivery = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $delivery_id LIMIT 1"
));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delivery Boy Profile</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
.profile-card {
    max-width: 850px;
    margin: auto;
    border-radius: 12px;
    background: #ffffff;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
.profile-header { display: flex; align-items: center; gap: 20px; }
.profile-img {
    width: 120px; height: 120px; background: #e8e8e8;
    border-radius: 50%; display: flex; justify-content: center;
    align-items: center; font-size: 48px; color: #5a5a5a;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.profile-name h3 { margin: 0; font-weight: 700; }
.info-table th { width: 180px; background: #f8f9fa; }
</style>
</head>

<body>
<div class="container mt-4">
    <div class="profile-card">

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success text-center">
                <i class="fa-solid fa-check-circle"></i> <?php echo $_GET['success']; ?>
            </div>
        <?php } ?>

        <div class="profile-header mb-4">
            <div class="profile-img">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile-name">
                <h3><?php echo $delivery['First_Name'] . " " . $delivery['Last_Name']; ?></h3>
                <p class="text-muted mb-0">
                    <i class="fa-solid fa-truck-fast"></i> <?php echo $delivery['User_Role']; ?>
                </p>
            </div>
        </div>

        <table class="table table-bordered info-table">
            <tr><th>User ID</th><td><?php echo $delivery['User_Id']; ?></td></tr>
            <tr><th>Email</th><td><?php echo $delivery['Email']; ?></td></tr>
            <tr><th>Phone</th><td><?php echo $delivery['Phone']; ?></td></tr>
            <tr><th>DOB</th><td><?php echo $delivery['DOB']; ?></td></tr>
            <tr><th>Address</th><td><?php echo $delivery['Address']; ?></td></tr>
            <tr><th>Pincode</th><td><?php echo $delivery['Pincode']; ?></td></tr>
            <tr><th>Created At</th><td><?php echo $delivery['Create_At']; ?></td></tr>
        </table>

        <div class="text-end">
            <a href="deliveryboy/edit_deliveryboy_profile.php" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
        </div>
    </div>
</div>
</body>
</html>

<?php
session_start();
include("../AdminPanel/db.php");

// CHECK LOGIN
if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php?popup=1");
    exit();
}

$uid = $_SESSION["User_Id"];

// FETCH USER
$query = "SELECT * FROM user_details WHERE User_Id='$uid' LIMIT 1";
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);

if (!$user || !is_array($user)) {
    echo "<h2 style='color:red;'>ERROR: User data not found.</h2>";
    exit();
}
$profileUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | GiftShop</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f3f4f6;
    margin: 0;
    padding: 0;
}

.account-wrapper {
    width: 92%;
    max-width: 1300px;
    margin: 30px auto;
    display: flex;
    gap: 20px;
}
.account-sidebar {
    width: 260px;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
}

.account-sidebar h3 {
    margin-bottom: 18px;
    font-size: 20px;
    color: #333;
}

.account-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.account-sidebar li {
    margin: 12px 0;
}

.account-sidebar a {
    text-decoration: none;
    color: #444;
    font-size: 16px;
    transition: 0.2s;
    display: block;
    padding: 8px 5px;
    border-radius: 5px;
}

.account-sidebar a:hover {
    background: #ffe6f0;
    color: #d6005c;
}

.account-content {
    flex: 1;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
}

.account-content h2 {
    font-size: 22px;
    margin-bottom: 20px;
    color: #333;
}

.profile-row {
    margin-bottom: 12px;
}

.profile-row b {
    width: 160px;
    display: inline-block;
    color: #555;
}

@media (max-width: 850px) {
    .account-wrapper {
        flex-direction: column;
    }
    .account-sidebar {
        width: 100%;
    }
}

</style>
<link rel="stylesheet" href="../home page/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<?php include("../home page/navbar.php"); ?>

<div class="account-wrapper">

    <!-- SIDEBAR -->
    <div class="account-sidebar">
        <h3>Hello, <?= htmlspecialchars($profileUser["First_Name"] . " " . $profileUser["Last_Name"]); ?></h3>

        <ul>
            <li><a href="profile.php">Dashboard</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <li><a href="../login/logout.php" style="color:red;">Logout</a></li>
        </ul>
    </div>

    <!-- MAIN PROFILE CONTENT -->
    <div class="account-content">
        <h2>My Profile</h2>

        <div class="profile-row">
            <b>Name:</b> <?= htmlspecialchars($profileUser["First_Name"] . " " . $profileUser["Last_Name"]); ?>
        </div>

        <div class="profile-row">
            <b>Email:</b> <?= htmlspecialchars($profileUser["Email"]); ?>
        </div>

        <div class="profile-row">
            <b>Phone:</b> <?= htmlspecialchars($profileUser["Phone"]); ?>
        </div>

        <div class="profile-row">
            <b>Date of Birth:</b> <?= htmlspecialchars($profileUser["DOB"]); ?>
        </div>

        <div class="profile-row">
            <b>Address:</b> <?= htmlspecialchars($profileUser["Address"]); ?>
        </div>

        <div class="profile-row">
            <b>Pincode:</b> <?= htmlspecialchars($profileUser["Pincode"]); ?>
        </div>

        <div class="profile-row">
            <b>Account Created:</b> <?= htmlspecialchars($profileUser["Create_At"]); ?>
        </div>

    </div>

</div>
 <?php require_once '../home page/footer.php'; ?>
 
</body>
</html>

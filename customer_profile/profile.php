<?php
session_start();
include("../AdminPanel/db.php");

// CHECK LOGIN
if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php?popup=1");
    exit();
}

$uid = $_SESSION["User_Id"];

// FETCH USER DATA
$query = "SELECT * FROM user_details WHERE User_Id='$uid' LIMIT 1";
$result = mysqli_query($connection, $query);
$profileUser = mysqli_fetch_assoc($result);

if (!$profileUser || !is_array($profileUser)) {
    echo "<h2 style='color:red;'>ERROR: User data not found.</h2>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Account | GiftShop</title>

<link rel="stylesheet" href="../home page/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
body{
    background:white;
    /* background:#f3f4f6; */
    font-family: Arial, sans-serif;
}

.account-container{
    max-width:1200px;
    margin:30px auto;
    padding:10px;
}

.account-title{
    font-size:26px;
    font-weight:bold;
    margin-bottom:20px;
}

/* FORCE 3 CARDS PER ROW */
.account-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:20px;
}


.account-card{
   /* background: #f3f4f6; */
   border:1px solid #7e2626d5;
    border-radius:12px;
    padding:25px;
    box-shadow:0 4px 12px rgba(0,0,0,0.02);
    transition:0.3s;
    cursor:pointer;
}

.account-card:hover{
    transform:translateY(-4px);
}

.card-icon{
    font-size:32px;
    margin-bottom:15px;
}

.orders{ color:#f97316; }
.profile{ color:#2563eb; }
.feedback{ color:#16a34a; }
.logout{ color:#dc2626; }

.account-card h3{
    margin:0 0 6px;
    font-size:18px;
}

.account-card p{
    margin:0;
    font-size:14px;
    color:#666;
}
.password{ color:#9333ea; } /* Purple */

</style>

</head>

<body>

<?php include("../home page/navbar.php"); ?>

<div class="account-container">

    <div class="account-title">
        Hello, <?= htmlspecialchars($profileUser["First_Name"]); ?> &#128075;
    </div>

    <div class="account-grid">

    <!-- 1. My Orders -->
    <div class="account-card" onclick="location.href='orders.php'">
        <div class="card-icon orders"><i class="fa-solid fa-box"></i></div>
        <h3>My Orders</h3>
        <p>Track & manage your orders</p>
    </div>

    <!-- 2. My Profile -->
    <div class="account-card" onclick="location.href='edit_profile.php'">
        <div class="card-icon profile"><i class="fa-solid fa-user"></i></div>
        <h3>My Profile</h3>
        <p>View & edit your profile</p>
    </div>

    <!-- 3. Password -->
    <div class="account-card" onclick="location.href='change_password.php'">
        <div class="card-icon password"><i class="fa-solid fa-lock"></i></div>
        <h3>Password</h3>
        <p>Change your password</p>
    </div>

    <!-- 5. Logout -->
    <div class="account-card" onclick="location.href='../login/logout.php'">
        <div class="card-icon logout"><i class="fa-solid fa-right-from-bracket"></i></div>
        <h3>Logout</h3>
        <p>Sign out securely</p>
    </div>

</div>


</div>

    </div>
</div>

<?php require_once '../home page/footer.php'; ?>

</body>
</html>

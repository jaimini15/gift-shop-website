<?php 
session_start();
include("../../AdminPanel/db.php");

if (!isset($_SESSION['delivery_otp_verified']) || $_SESSION['delivery_otp_verified'] !== true) {
    die("Unauthorized Access");
}

$email = $_SESSION['delivery_reset_email'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Create New Password</title>

<style>
    body { font-family: Arial; background: #f5f5f5; }
    .box { width: 350px; background: white; padding: 25px; margin: 80px auto; border-radius: 8px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 90%; padding: 10px; margin: 10px 0; }
    button { width: 100%; padding: 10px; background: #007bff; color: white; border: none;
             border-radius: 4px; cursor: pointer; }
</style>

</head>
<body>

<div class="box">
<h2>Create New Password</h2>

<?php
if (isset($_POST['update'])) {

    $pass = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($pass !== $confirm) {
        echo "<p style='color:red;'>Passwords do not match!</p>";
    } else {

        mysqli_query($connection,
            "UPDATE user_details SET Password='$pass', otp=NULL WHERE Email='$email'"
        );

        unset($_SESSION['delivery_reset_email']);
        unset($_SESSION['delivery_otp_verified']);

        echo "<p style='color:green;'>Password updated! Redirecting...</p>";
        echo "<script>setTimeout(()=>{ window.location='login.php'; }, 1500);</script>";
    }
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="Enter New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button type="submit" name="update">Update Password</button>
</form>

</div>
</body>
</html>

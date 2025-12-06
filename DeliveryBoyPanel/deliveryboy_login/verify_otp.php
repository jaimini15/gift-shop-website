<?php 
session_start();
include("../../AdminPanel/db.php");

if (!isset($_SESSION['delivery_reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['delivery_reset_email'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>

<style>
    body { font-family: Arial; background: #f5f5f5; }
    .box { width: 350px; background: #fff; padding: 25px; margin: 80px auto; border-radius: 8px;
           box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 90%; padding: 10px; margin: 10px 0; }
    button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; 
             border-radius: 4px; cursor: pointer; }
</style>

</head>

<body>

<div class="box">
<h2>Verify OTP</h2>

<?php
if (isset($_POST['verify'])) {

    $otp_entered = mysqli_real_escape_string($connection, $_POST['otp']);

    $query = "SELECT * FROM user_details WHERE Email='$email' AND otp='$otp_entered' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {

        mysqli_query($connection, "UPDATE user_details SET otp=NULL WHERE Email='$email'");

        $_SESSION['delivery_otp_verified'] = true;

        echo "<p style='color:green;'>OTP Verified!</p>";
        echo "<script>setTimeout(()=>{ window.location='create_new_password.php'; }, 1500);</script>";

    } else {
        echo "<p style='color:red;'>Incorrect OTP!</p>";
    }
}
?>

<form method="POST">
    <input type="number" name="otp" placeholder="Enter OTP" required>
    <button type="submit" name="verify">Verify OTP</button>
</form>
<br>
<center><a href="forgot_password.php">Back</a></center>

</div>
</body>
</html>

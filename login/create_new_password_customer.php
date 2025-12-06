<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['reset_email_customer']) || !isset($_SESSION['otp_verified_customer'])) {
    header("Location: forgot_password_customer.php");
    exit;
}

$email = $_SESSION['reset_email_customer'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Create New Password - Customer</title>
<style>
    body { font-family: Arial; background: #f5f5f5; }
    .box { width: 350px; background: #fff; padding: 25px; margin: 80px auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 90%; padding: 10px; margin: 10px 0; font-size: 15px; }
    button { width: 100%; padding: 10px; background: #007bff; border: 0; color: #fff; font-size: 16px; cursor: pointer; border-radius: 4px; }
    button:hover { background: #0056b3; }
    .msg { font-size: 15px; margin-bottom: 10px; }
    a { font-size: 14px; text-decoration: none; color: #007bff; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="box">
<h2>Create New Password</h2>

<?php
if (isset($_POST['change'])) {
    $newpass = mysqli_real_escape_string($connection, $_POST['newpass']);
    $confirmpass = mysqli_real_escape_string($connection, $_POST['confirmpass']);

    if ($newpass != $confirmpass) {
        echo "<p class='msg' style='color:red;'>Passwords do not match!</p>";
    } else {
        mysqli_query($connection, "UPDATE user_details SET Password='$newpass', otp=NULL WHERE Email='$email'");

        unset($_SESSION['reset_email_customer']);
        unset($_SESSION['otp_verified_customer']);

        echo "<p class='msg' style='color:green;'>Password updated successfully!</p>";
        echo "<script>setTimeout(function(){ window.location='../login/login.php'; }, 1500);</script>";
    }
}
?>

<form method="POST">
    <input type="password" name="newpass" placeholder="Enter New Password" required>
    <input type="password" name="confirmpass" placeholder="Confirm Password" required>
    <button type="submit" name="change">Change Password</button>
</form>
<br>
<center><a href="../login/login.php">Back to Login</a></center>
</div>

</body>
</html>

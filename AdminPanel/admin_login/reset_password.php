<?php
session_start();
include("../db.php");

if(!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true){
    die("Unauthorized Access");
}

$email = $_SESSION['reset_email'];

if(isset($_POST['update'])){

    $new_password = $_POST['password'];

    mysqli_query($connection, 
        "UPDATE user_details SET Password='$new_password', otp=NULL WHERE Email='$email'"
    );

    unset($_SESSION['reset_email']);
    unset($_SESSION['otp_verified']);

    echo "<p style='color:green;'>Password updated successfully! <a href='login.php'>Login</a></p>";
}
?>

<!DOCTYPE html>
<html>
<body>
<h2>Create New Password</h2>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit" name="update">Update Password</button>
</form>

</body>
</html>

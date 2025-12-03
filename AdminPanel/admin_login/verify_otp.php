<?php 
session_start();
include("../db.php");

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>

<style>
    body {
        font-family: Arial;
        background: #f5f5f5;
    }
    .box {
        width: 350px;
        background: #fff;
        padding: 25px;
        margin: 80px auto;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input {
        width: 90%;
        padding: 10px;
        margin: 10px 0;
        font-size: 15px;
    }
    button {
        width: 100%;
        padding: 10px;
        background: #007bff;
        border: 0;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
    }
    button:hover {
        background: #0056b3;
    }
    a {
        font-size: 14px;
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    .msg {
        font-size: 15px;
        margin-bottom: 10px;
    }
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

            echo "<p class='msg' style='color:green;'>OTP Verified!</p>";

            echo "<script>
                    setTimeout(function(){
                        window.location='create_new_password.php';
                    }, 1500);
                  </script>";

        } else {
            echo "<p class='msg' style='color:red;'>Incorrect OTP!</p>";
        }
    }
    ?>

    <form method="POST">
        <input type="number" name="otp" placeholder="Enter OTP" required>
        <button type="submit" name="verify">Verify OTP</button>
    </form>

    <center><a href="forgot_password.php">Back</a></center>
</div>

</body>
</html>

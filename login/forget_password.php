<?php 
session_start();
include("../AdminPanel/db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
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
input, select { 
    width: 95%; 
    padding: 10px; 
    margin: 10px 0; 
    font-size: 15px; 
    border: 1px solid #ccc;
    border-radius: 4px;
}
input:focus, select:focus {
    outline: none;
    border-color: #7e2626d5;
}
button { 
    width: 100%; 
    padding: 10px; 
    background: #7e2626d5; 
    border: 0; 
    color: #fff; 
    font-size: 16px; 
    cursor: pointer; 
    border-radius: 4px; 
}
button:hover { 
    background: black; 
}
.msg { 
    font-size: 15px; 
    margin-bottom: 10px; 
}
.back-link { 
    text-align: center; 
    margin-top: 10px; 
}
</style>

</head>
<body>

<div class="box">
<h2>Forgot Password</h2>

<?php
if (isset($_POST['send_otp'])) {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $role  = mysqli_real_escape_string($connection, $_POST['role']);

    $query = "SELECT * FROM user_details WHERE Email='$email' AND User_Role='$role' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {

        $otp = rand(100000, 999999);
       $stmt = $connection->prepare("UPDATE user_details SET otp=? WHERE Email=? AND User_Role=?");
$stmt->bind_param("iss", $otp, $email, $role);

        $stmt->execute();
        $stmt->close();

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'giftshopmaninagar@gmail.com';
            $mail->Password = 'ljoy otkw cvnk beqi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('giftshopmaninagar@gmail.com', 'GiftShop System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP is: <b>$otp</b>";

            $mail->send();

            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_role']  = $role;

            echo "<p class='msg' style='color:green;'>OTP sent successfully!</p>";
            echo "<script>setTimeout(()=>window.location='verify_otp.php',1500);</script>";

        } catch (Exception $e) {
            echo "<p class='msg' style='color:red;'>Email error!</p>";
        }

    } else {
        echo "<p class='msg' style='color:red;'>Email not found for selected role!</p>";
    }
}
?>

<form method="POST">
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="CUSTOMER">Customer</option>
        <option value="ADMIN">Admin</option>
        <option value="DELIVERY_BOY">Delivery Boy</option>
    </select>

    <input type="email" name="email" placeholder="Enter Email" required>
    <button type="submit" name="send_otp">Send OTP</button>
</form>

</div>
</body>
</html>

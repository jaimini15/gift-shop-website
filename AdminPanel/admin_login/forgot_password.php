<?php 
session_start();
include(__DIR__ . '/../db.php');

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../PHPMailer-master/src/SMTP.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .box { width: 350px; background: #fff; padding: 25px; margin: 80px auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 90%; padding: 10px; margin: 10px 0; font-size: 15px; }
        button { width: 100%; padding: 10px; background: #007bff; border: 0; color: #fff; font-size: 16px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
        .msg { font-size: 15px; margin-bottom: 10px; }
        .back-link { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Forgot Password</h2>

<?php
if (isset($_POST['send_otp'])) {

    $email = mysqli_real_escape_string($connection, $_POST['email']);

    // Check admin email exists
    $query = "SELECT * FROM user_details WHERE Email='$email' AND User_Role='ADMIN' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {

        // Generate OTP
        $otp = rand(100000, 999999);

        // Update OTP in database
        $stmt = $connection->prepare("UPDATE user_details SET otp=? WHERE Email=?");
        $stmt->bind_param("is", $otp, $email);
        $stmt->execute();
        $stmt->close();

        $mail = new PHPMailer(true);

        try {

            // SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // ⚠️ ENTER YOUR NEW APP PASSWORD HERE
            $mail->Username = 'giftshopmaninagar@gmail.com';
            $mail->Password = 'ljoy otkw cvnk beqi';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Fix SSL issues in XAMPP
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Send mail
            $mail->setFrom('yourgmail@gmail.com', 'GiftShop System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Admin Password Reset OTP';
            $mail->Body = "Your OTP for password reset is: <b>$otp</b>";

            $mail->send();

            echo "<p class='msg' style='color:green;'>OTP sent to your email!</p>";

            $_SESSION['reset_email'] = $email;

            echo "<script>
                    setTimeout(function(){
                        window.location='verify_otp.php';
                    }, 1500);
                  </script>";

        } catch (Exception $e) {
            echo "<p class='msg' style='color:red;'>Error sending email: {$mail->ErrorInfo}</p>";
        }

    } else {
        echo "<p class='msg' style='color:red;'>Admin email not found!</p>";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter Admin Email" required>
    <button type="submit" name="send_otp">Send OTP</button>
</form>

<div class="back-link">
    <a href="login.php">Back to Login</a>
</div>

</div>

</body>
</html>

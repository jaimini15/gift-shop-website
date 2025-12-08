<?php
session_start();
include("../../AdminPanel/db.php");

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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    </style>
</head>

<body>

    <div class="box">
        <h2>Forgot Password</h2>

        <?php
        if (isset($_POST['send_otp'])) {

            $email = mysqli_real_escape_string($connection, $_POST['email']);

            // Check email exists for DELIVERY_BOY
            $query = "SELECT * FROM user_details WHERE Email='$email' AND User_Role='DELIVERY_BOY' LIMIT 1";
            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) == 1) {

                $otp = rand(100000, 999999);

                $stmt = $connection->prepare("UPDATE user_details SET otp=? WHERE Email=?");
                $stmt->bind_param("is", $otp, $email);
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

                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    $mail->setFrom('giftshopmaninagar@gmail.com', 'GiftShop Delivery System');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Delivery Boy Password Reset OTP';
                    $mail->Body = "Your OTP for resetting password is: <b>$otp</b>";

                    $mail->send();

                    $_SESSION['delivery_reset_email'] = $email;

                    echo "<p style='color:green;'>OTP sent to email!</p>";
                    echo "<script>setTimeout(()=>{ window.location='verify_otp.php'; }, 1500);</script>";

                } catch (Exception $e) {
                    echo "<p style='color:red;'>Email error: {$mail->ErrorInfo}</p>";
                }

            } else {
                echo "<p style='color:red;'>Delivery boy email not found!</p>";
            }
        }
        ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter Delivery Boy Email" required>
            <button type="submit" name="send_otp">Send OTP</button>
        </form>

        <div class="back-link" style="text-align:center; margin-top:10px;">
            <a href="login.php" style="font-size:15px; font-weight:bold;">
                 Back to Login
            </a>
        </div>

    </div>
</body>

</html>

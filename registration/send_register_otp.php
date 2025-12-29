<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();
include("../AdminPanel/db.php");
include("../PHPMailer/PHPMailer.php");
include("../PHPMailer/SMTP.php");
include("../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(["success"=>false,"message"=>"Email required"]);
    exit;
}

// Prevent already registered email
$check = mysqli_query($connection, "SELECT Email FROM user_details WHERE Email='$email' LIMIT 1");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success"=>false,"message"=>"Email already registered"]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);

// Store ONLY in session (temporary)
$_SESSION['register_otp'] = $otp;
$_SESSION['register_email'] = $email;
$_SESSION['register_otp_time'] = time(); // for expiry

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "giftshopmaninagar@gmail.com"; // your Gmail
    $mail->Password = "ljoy otkw cvnk beqi";       // Gmail App Password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("giftshopmaninagar@gmail.com", "GiftShop");
    $mail->addAddress($email);
    $mail->Subject = "Email Verification OTP";
    $mail->Body = "Your OTP for registration is: $otp";

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "OTP sent to your email"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Mailer Error: " . $mail->ErrorInfo
    ]);
}
?>

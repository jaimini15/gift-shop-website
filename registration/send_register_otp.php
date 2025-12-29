<?php
session_start();
include("../AdminPanel/db.php");
include("../PHPMailer/PHPMailer.php");
include("../PHPMailer/SMTP.php");
include("../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;

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
$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->Username = "yourmail@gmail.com";
$mail->Password = "app_password";
$mail->SMTPSecure = "tls";
$mail->Port = 587;

$mail->setFrom("yourmail@gmail.com", "GiftShop");
$mail->addAddress($email);
$mail->Subject = "Email Verification OTP";
$mail->Body = "Your OTP for registration is: $otp";

$mail->send();

echo json_encode([
    "success" => true,
    "message" => "OTP sent to your email"
]);

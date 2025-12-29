<?php
session_start();
include("../AdminPanel/db.php");
include("../PHPMailer/PHPMailer.php");
include("../PHPMailer/SMTP.php");
include("../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 0); // prevent warnings
header('Content-Type: application/json'); // JSON output
$mail = new PHPMailer(true);

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(["success" => false, "message" => "Email required"]);
    exit;
}

// Check email exists
$check = mysqli_query($connection, "SELECT Email FROM user_details WHERE Email='$email' LIMIT 1");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered"]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['register_otp'] = $otp;
$_SESSION['register_email'] = $email;
$_SESSION['register_otp_time'] = time();

try {
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // important: no debug output
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "giftshopmaninagar@gmail.com";
    $mail->Password = 'ljoy otkw cvnk beqi'; // your app password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("giftshopmaninagar@gmail.com", "GiftShop");
    $mail->addAddress($email);
    $mail->Subject = "Email Verification OTP";
    $mail->Body = "Your OTP for registration is: $otp";

    $mail->send();

    echo json_encode(["success" => true, "message" => "OTP sent to your email"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Mailer Error"]);
}
// ❌ DO NOT close with ?>

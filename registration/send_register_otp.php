<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);

include("../AdminPanel/db.php");

// ✅ CORRECT PHPMailer PATHS
require_once __DIR__ . "/../PHPMailer-master/src/PHPMailer.php";
require_once __DIR__ . "/../PHPMailer-master/src/SMTP.php";
require_once __DIR__ . "/../PHPMailer-master/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(["success"=>false,"message"=>"Email required"]);
    exit;
}

// Check email exists
$check = mysqli_query($connection,"SELECT Email FROM user_details WHERE Email='$email'");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success"=>false,"message"=>"Email already registered"]);
    exit;
}

// Generate OTP
$otp = rand(100000,999999);
$_SESSION['register_otp'] = $otp;
$_SESSION['register_otp_time'] = time();

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = 'giftshopmaninagar@gmail.com';
    $mail->Password = 'ljoy otkw cvnk beqi';
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom('giftshopmaninagar@gmail.com', 'GiftShop System');
    $mail->addAddress($email);
    $mail->Subject = "OTP Verification";
    $mail->Body = "Your OTP is: $otp";

    $mail->send();

    echo json_encode(["success"=>true,"message"=>"OTP sent successfully"]);

} catch (Exception $e) {
    echo json_encode(["success"=>false,"message"=>"Email sending failed"]);
}

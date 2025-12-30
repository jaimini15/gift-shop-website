<?php
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$otp = trim($data['otp'] ?? '');
if (!$otp) {
    echo json_encode(["success"=>false,"message"=>"OTP required"]);
    exit;
}
if (!isset($_SESSION['register_otp']) ||
    time() - $_SESSION['register_otp_time'] > 300) {
    echo json_encode(["success"=>false,"message"=>"OTP expired"]);
    exit;
}
if ($otp == $_SESSION['register_otp']) {
    $_SESSION['register_verified'] = true;
    echo json_encode(["success"=>true,"message"=>"Email verified"]);
} else {
    echo json_encode(["success"=>false,"message"=>"Invalid OTP"]);
}
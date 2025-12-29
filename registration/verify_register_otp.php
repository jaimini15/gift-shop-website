<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$otpEntered = trim($data['otp'] ?? '');

if (!$otpEntered) {
    echo json_encode(["success"=>false,"message"=>"OTP required"]);
    exit;
}

// OTP expiry: 5 minutes
if (!isset($_SESSION['register_otp_time']) || time() - $_SESSION['register_otp_time'] > 300) {
    unset($_SESSION['register_otp'], $_SESSION['register_email'], $_SESSION['register_otp_time']);
    echo json_encode(["success"=>false,"message"=>"OTP expired"]);
    exit;
}

if (
    isset($_SESSION['register_otp']) &&
    $otpEntered == $_SESSION['register_otp']
) {
    $_SESSION['register_verified'] = true;

    echo json_encode([
        "success" => true,
        "message" => "Email verified successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid OTP"
    ]);
}

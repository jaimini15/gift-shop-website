<?php
session_start();
include("../db.php");

// Always return JSON for AJAX
header("Content-Type: application/json");

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Email or Password missing"
    ]);
    exit;
}

$email    = mysqli_real_escape_string($connection, $_POST['email']);
$password = mysqli_real_escape_string($connection, $_POST['password']);

$query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
$result = mysqli_query($connection, $query);

// Query error
if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($result);

// No user found
if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Email"
    ]);
    exit;
}

// Password check (plain text)
if ($password === $user['Password']) {

    // Save user session
    $_SESSION['user_id'] = $user['User_Id'];
    $_SESSION['email']   = $user['Email'];

    // ⭐ Check if user clicked Buy Now earlier
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    } else {
        $redirect = "../product_page/product_list.php";
    }

    echo json_encode([
        "success" => true,
        "message" => "Login Successful!",
        "redirect" => $redirect
    ]);
    exit;
}

// Wrong password
echo json_encode([
    "success" => false,
    "message" => "Incorrect Password"
]);
exit;
?>

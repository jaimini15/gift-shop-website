<?php
session_start();
include("../db.php");

// Check missing fields
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header("Location: login.php?error=Email or Password missing");
    exit;
}

$email    = mysqli_real_escape_string($connection, $_POST['email']);
$password = mysqli_real_escape_string($connection, $_POST['password']);

$query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
$result = mysqli_query($connection, $query);

// Query error
if (!$result) {
    header("Location: login.php?error=Database error");
    exit;
}

$user = mysqli_fetch_assoc($result);

// No user found
if (!$user) {
    header("Location: login.php?error=Invalid Email");
    exit;
}

// Password check (TEXT as you stored)
if ($password === $user['Password']) {

    $_SESSION['user_id'] = $user['User_Id'];
    $_SESSION['email']   = $user['Email'];

    // ⭐ REDIRECT TO PRODUCT LIST PAGE
    header("Location: ../product_page/product_list.php");
    exit;
}

// Wrong password
header("Location: login.php?error=Incorrect Password");
exit;
?>

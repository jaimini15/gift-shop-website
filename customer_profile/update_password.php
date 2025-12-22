<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

$current = $_POST['current_password'];
$new     = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

// Fetch current password
$result = mysqli_query($connection,
    "SELECT Password FROM user_details WHERE User_Id='$uid' LIMIT 1"
);
$row = mysqli_fetch_assoc($result);

// 1. Check current password
if ($row['Password'] !== $current) {
    header("Location: change_password.php?error=1");
    exit();
}

// 2. Check new passwords match
if ($new !== $confirm) {
    header("Location: change_password.php?error=2");
    exit();
}

// 3. Update password
mysqli_query($connection,
    "UPDATE user_details SET Password='$new' WHERE User_Id='$uid'"
);

// 4. Redirect to profile/dashboard
header("Location: profile.php?password=success");
exit();

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

// Fetch current password from DB
$result = mysqli_query(
    $connection,
    "SELECT Password FROM user_details WHERE User_Id='$uid' LIMIT 1"
);

$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: change_password.php?error=4");
    exit();
}

/* ================= VALIDATIONS ================= */

// Check current password
if ($row['Password'] !== $current) {
    header("Location: change_password.php?error=1");
    exit();
}

// Check new & confirm match
if ($new !== $confirm) {
    header("Location: change_password.php?error=2");
    exit();
}

// Check new password is NOT same as old password
if ($new === $row['Password']) {
    header("Location: change_password.php?error=3");
    exit();
}

/* UPDATE PASSWORD */

mysqli_query(
    $connection,
    "UPDATE user_details SET Password='$new' WHERE User_Id='$uid'"
);
header("Location: profile.php?password=success");
exit();

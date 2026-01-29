<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

$fname   = mysqli_real_escape_string($connection, $_POST['fname']);
$lname   = mysqli_real_escape_string($connection, $_POST['lname']);
$phone   = mysqli_real_escape_string($connection, $_POST['phone']);
$address = mysqli_real_escape_string($connection, $_POST['address']);
$area_id = mysqli_real_escape_string($connection, $_POST['area_id']); // 
$dob     = mysqli_real_escape_string($connection, $_POST['dob']);

mysqli_query($connection, "
    UPDATE user_details SET
        First_Name = '$fname',
        Last_Name  = '$lname',
        Phone      = '$phone',
        Address    = '$address',
        Area_Id    = '$area_id',   -- 
        DOB        = '$dob'
    WHERE User_Id = '$uid'
");

header("Location: profile.php?updated=1");
exit();

<?php
session_start();
include("../AdminPanel/db.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.php");
    exit();
}

$first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
$last_name  = mysqli_real_escape_string($connection, $_POST['last_name']);
$dob        = mysqli_real_escape_string($connection, $_POST['dob']);
$phone      = mysqli_real_escape_string($connection, $_POST['phone']);
$address    = mysqli_real_escape_string($connection, $_POST['address']);
$area_id = mysqli_real_escape_string($connection, $_POST['area_id']);
$email      = mysqli_real_escape_string($connection, $_POST['email']);
$password   = mysqli_real_escape_string($connection, $_POST['password']);
$role       = "CUSTOMER";

/* 🔍 CHECK EMAIL */
$checkEmail = mysqli_query($connection, "SELECT Email FROM user_details WHERE Email='$email'");
if (mysqli_num_rows($checkEmail) > 0) {
    echo "<script>
        alert('Email already registered! Please login.');
        window.location.href='../login/login.php';
    </script>";
    exit();
}

/* 🔍 CHECK PHONE */
$checkPhone = mysqli_query($connection, "SELECT Phone FROM user_details WHERE Phone='$phone'");
if (mysqli_num_rows($checkPhone) > 0) {
    echo "<script>
        alert('Phone number already exists! Please login.');
        window.location.href='../login/login.php';
    </script>";
    exit();
}

/* ✅ INSERT USER */
$insert = "INSERT INTO user_details 
(First_Name, Last_Name, DOB, Phone, Address, Area_Id, Email, Password, User_Role, Create_At)
VALUES 
('$first_name','$last_name','$dob','$phone','$address','$area_id','$email','$password','$role',NOW())";
if (mysqli_query($connection, $insert)) {
    echo "<script>
        alert('Registration successful! Please login.');
        window.location.href='../login/login.php';
    </script>";
} else {
    echo "<script>
        alert('Registration failed. Try again.');
        window.location.href='registration.php';
    </script>";
}
exit();

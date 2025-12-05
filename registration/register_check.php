<?php
session_start();
include("../AdminPanel/db.php");

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect and sanitize input
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($connection, $_POST['last_name']);
    $dob        = mysqli_real_escape_string($connection, $_POST['dob']);
    $phone      = mysqli_real_escape_string($connection, $_POST['phone']);
    $address    = mysqli_real_escape_string($connection, $_POST['address']);
    $pincode    = mysqli_real_escape_string($connection, $_POST['pincode']);
    $email      = mysqli_real_escape_string($connection, $_POST['email']);
    $password   = mysqli_real_escape_string($connection, $_POST['password']);
    $role       = "Customer";

    // Check if email already exists
    $checkEmail = "SELECT Email FROM user_details WHERE Email='$email' LIMIT 1";
    $emailResult = mysqli_query($connection, $checkEmail);

    if ($emailResult && mysqli_num_rows($emailResult) > 0) {
        // Email exists → redirect to login popup
        echo "<script>
                alert('Email already exists! Please login.');
                window.location.href='../login/login.php?popup=1';
              </script>";
        exit();
    }

    // Check if phone already exists (optional, avoids duplicate key error)
    $checkPhone = "SELECT Phone FROM user_details WHERE Phone='$phone' LIMIT 1";
    $phoneResult = mysqli_query($connection, $checkPhone);

    if ($phoneResult && mysqli_num_rows($phoneResult) > 0) {
        echo "<script>
                alert('Phone number already exists! Please login.');
                window.location.href='../login/login.php?popup=1';
              </script>";
        exit();
    }

    // Insert new user
    $insert = "INSERT INTO user_details 
               (First_Name, Last_Name, DOB, Phone, Address, Pincode, Email, Password, User_Role, Create_At)
               VALUES 
               ('$first_name','$last_name','$dob','$phone','$address','$pincode','$email','$password','$role',NOW())";

    if (mysqli_query($connection, $insert)) {

        // Redirect back to login popup after successful registration
        // The page where user clicked "Buy Now" is already saved in session
        $redirectPage = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : "../product_page/product_list.php";

        echo "<script>
                alert('Registration successful! Please login.');
                window.location.href='../login/login.php?popup=1';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Account creation failed. Please try again.');
                window.location.href='register.php';
              </script>";
        exit();
    }
} else {
    // If not POST request, redirect to register page
    header("Location: register.php");
    exit();
}

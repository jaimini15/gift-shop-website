<?php
session_start();
include("../AdminPanel/db.php");

if (
    !isset($_SESSION['register_verified']) ||
    $_SESSION['register_verified'] !== true
) {
    echo "<script>
        alert('Please verify your email with OTP first');
        window.location.href='register.php';
    </script>";
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

    if (mysqli_query($connection, $insert)) {

    // 🔥 CLEAN UP OTP SESSION (IMPORTANT)
    unset(
        $_SESSION['register_otp'],
        $_SESSION['register_email'],
        $_SESSION['register_otp_time'],
        $_SESSION['register_verified']
    );

    echo "<script>
            alert('Registration successful! Please login.');
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

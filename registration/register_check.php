<?php
session_start();
include("../AdminPanel/db.php");

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

    // Check email existence
    $checkEmail = "SELECT Email FROM user_details WHERE Email='$email' LIMIT 1";
    $emailResult = mysqli_query($connection, $checkEmail);

    if ($emailResult && mysqli_num_rows($emailResult) > 0) {
        echo "<script>
                alert('Email already exists! Please login.');
                window.location.href='../login/login.php?popup=1';
              </script>";
        exit();
    }

    $insert = "INSERT INTO user_details 
               (First_Name, Last_Name, DOB, Phone, Address, Pincode, Email, Password, User_Role, Create_At)
               VALUES 
               ('$first_name','$last_name','$dob','$phone','$address','$pincode','$email','$password','$role',NOW())";

    if (mysqli_query($connection, $insert)) {

    // Keep the redirect page for after login
    if (!isset($_SESSION['redirect_after_login'])) {
        $_SESSION['redirect_after_login'] = "../product_page/product_list.php?category_id=" . $_GET['category_id'];
    }

    echo "<script>
            alert('Registration Successful! Please login.');
            window.location.href='../login/login.php?popup=1';
          </script>";
    exit();
}

else {
        echo "<script>
                alert('Account creation failed.');
                window.location.href='registration.php?popup=1';
              </script>";
        exit();
    }
}
?>
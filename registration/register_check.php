<?php
session_start();
include("../AdminPanel/db.php"); // DB connection

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
        echo "<script>
                alert('Email already exists! Please login.');
                window.location.href='../login/login.php?popup=1';
              </script>";
        exit();
    }

    // Insert new user
    $insert = "
        INSERT INTO user_details 
        (First_Name, Last_Name, DOB, Phone, Address, Pincode, Email, Password, User_Role, Create_At)
        VALUES 
        ('$first_name', '$last_name', '$dob', '$phone', '$address', '$pincode', '$email', '$password', '$role', NOW())
    ";

    if (mysqli_query($connection, $insert)) {

        // Auto login
        $_SESSION['Email']     = $email;
        $_SESSION['User_Role'] = $role;
        $_SESSION['Name']      = $first_name;

        // Redirect if user clicked Buy Now
        if (isset($_SESSION['redirect_page']) && $_SESSION['redirect_page'] != "") {
            $page = "../" . $_SESSION['redirect_page'];
            unset($_SESSION['redirect_page']);
            header("Location: $page");
            exit;
        }

        // Default redirect
        header("Location: ../home page/index.php");
        exit;

    } else {
        echo "<script>
                alert('Account creation failed.');
                window.location.href='registration.php?popup=1';
              </script>";
        exit();
    }
}
?>

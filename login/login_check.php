<?php
session_start();
include("../AdminPanel/db.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Secure input
    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Fetch user record
    $query  = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        // Check password (plain text match)
        if ($password === $user['Password']) {

            // Save user data in session
            $_SESSION['User_Id']    = $user['User_Id'];
            $_SESSION['First_Name'] = $user['First_Name'];
            $_SESSION['Last_Name']  = $user['Last_Name'];
            $_SESSION['DOB']        = $user['DOB'];
            $_SESSION['User_Role']  = $user['User_Role'];
            $_SESSION['Phone']      = $user['Phone'];
            $_SESSION['Address']    = $user['Address'];
            $_SESSION['Pincode']    = $user['Pincode'];
            $_SESSION['Email']      = $user['Email'];
            $_SESSION['Create_At']  = $user['Create_At'];

            // Redirect back to saved page after login
            if (isset($_SESSION['redirect_after_login'])) {

                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);

                header("Location: ../$redirect");
                exit();
            }

            // Default redirect (if no redirect saved)
            header("Location: ../AdminPanel/layout.php?view=dashboard");
            exit();
        }
    }

    // Invalid login
    echo "<script>
            alert('Invalid Email or Password! Please register first if you are a new user.');
            window.location.href = '/GIFT-SHOP-WEBSITE/login/login.php';
          </script>";
    exit();
}
?>

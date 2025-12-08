<?php
session_start();
include("../../AdminPanel/db.php");  // Correct Path

if (isset($_POST['login'])) {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Fetch user
    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check role = DELIVERY_BOY
        if ($user['User_Role'] !== "DELIVERY_BOY") {
            header("Location: login.php?error=Access Denied");
            exit();
        }

        // Password check
        if ($password === $user['Password']) {

            $_SESSION['delivery_id']   = $user['User_Id'];
            $_SESSION['delivery_name'] = $user['First_Name'];
            $_SESSION['delivery_role'] = $user['User_Role'];

            // Remember Me
            if (isset($_POST['remember'])) {
                setcookie("delivery_email", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("delivery_email", "", time() - 3600, "/");
            }

            header("Location: ../layout.php");
            exit();
        }
    }

    header("Location: login.php?error=Invalid Credentials");
    exit();
}
?>

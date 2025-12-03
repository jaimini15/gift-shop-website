<?php
session_start();
include("../db.php");   // FIXED PATH: only one level up

if (isset($_POST['login'])) {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // User table
    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check role = ADMIN
        if ($user['User_Role'] !== "ADMIN") {
            header("Location: login.php?error=Access Denied");
            exit();
        }

        // Password check (simple comparison)
        if ($password === $user['Password']) {

            // SESSION SET
            $_SESSION['admin_id']   = $user['User_Id'];
            $_SESSION['admin_name'] = $user['First_Name'];
            $_SESSION['admin_role'] = $user['User_Role'];

            // REMEMBER ME
            if (isset($_POST['remember'])) {
                setcookie("admin_email", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("admin_email", "", time() - 3600, "/");
            }

            // Redirect to layout.php
            header("Location: ../layout.php");
            exit();
        }
    }

    header("Location: login.php?error=Invalid Credentials");
    exit();
}
?>

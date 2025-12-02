<?php
session_start();
include("../../db.php");  // go two steps back to root db.php

if(isset($_POST['login'])) {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query = "SELECT * FROM admins WHERE email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        if ($password === $admin['password']) {

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: ../dashboard/dashboard.php");
            exit();
        }
    }

    header("Location: login.php?error=Invalid Credentials");
    exit();
}
?>

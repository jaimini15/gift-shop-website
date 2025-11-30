<?php
session_start();

// If not logged in → redirect to login page
if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}
?>

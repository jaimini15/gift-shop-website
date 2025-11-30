<?php
session_start();

if (!isset($_SESSION['User_Id'])) {
    header("Location: ../../login/login.php");
    exit;
}
?>

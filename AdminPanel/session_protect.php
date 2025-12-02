<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: AdminPanel/admin login/login.php");
    exit();
}
?>

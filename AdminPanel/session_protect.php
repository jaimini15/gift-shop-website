<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login/login.php");
    exit();
}

<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Remove remember me cookie
if (isset($_COOKIE['admin_email'])) {
    setcookie("admin_email", "", time() - 3600, "/");
}

// Redirect to login page
header("Location: login.php");
exit;
?>

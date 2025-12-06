<?php
session_start();

// Destroy all delivery boy session data
session_unset();
session_destroy();

// Remove remember me cookie if exists
if (isset($_COOKIE['delivery_email'])) {
    setcookie("delivery_email", "", time() - 3600, "/");
}

// Redirect back to delivery boy login page
header("Location: login.php");
exit;
?>

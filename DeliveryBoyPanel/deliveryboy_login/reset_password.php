<?php
session_start();
include("../db.php");

// Security check — Only allow access if OTP was verified
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: forgot_password.php?error=Unauthorized Access");
    exit;
}

$email = $_SESSION['reset_email'];

$message = "";

// Update password
if (isset($_POST['update'])) {

    $new_password = trim($_POST['password']);

    if ($new_password == "") {
        $message = "<p style='color:red;'>Password cannot be empty!</p>";
    } else {

        // Since you said password is stored in plain text
        $query = "UPDATE user_details 
                  SET Password='$new_password', otp=NULL 
                  WHERE Email='$email'";

        if (mysqli_query($connection, $query)) {

            // Clear sessions
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);

            $message = "<p style='color:green;'>Password updated successfully! 
                        <a href='login.php'>Login</a></p>";
        } 
        else {
            $message = "<p style='color:red;'>Something went wrong! Try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Password</title>
</head>
<body>

<h2>Create New Password</h2>

<?php echo $message; ?>

<form method="POST">
    <input type="password" name="password" placeholder="Enter New Password" required>
    <button type="submit" name="update">Update Password</button>
</form>

</body>
</html>

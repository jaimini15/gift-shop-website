<?php
session_start();
include("../AdminPanel/db.php"); // Database connection

// Detect if opened as popup from another page
$isPopup = isset($embedded) ? true : false;

// Handle login form submission
$login_error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query  = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Plain text password check (change to hash in production)
        if ($password === $user['Password']) {

            // Save session
            $_SESSION['User_Id']   = $user['User_Id'];
            $_SESSION['Email']     = $user['Email'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Redirect after login if set
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: ../$redirect");
                exit();
            }

            // Default redirect
            header("Location: ../AdminPanel/layout.php?view=dashboard");
            exit();
        }
    }

    // Invalid login
    $login_error = "Invalid Email or Password! Please register first if you are a new user.";
}
?>

<?php if (!$isPopup): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Gift Shop</title>
<link rel="stylesheet" href="../home page/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<?php endif; ?>

<style>
body { margin:0; font-family: Arial,sans-serif; }
.overlay {
    position: <?= $isPopup ? "fixed" : "relative" ?>;
    top:0; left:0; width:100%; height:100%;
    <?= $isPopup ? "backdrop-filter: blur(10px); background: rgba(0,0,0,0.3);" : "" ?>;
    display:flex; justify-content:center; align-items:center;
    z-index:9999;
}
.login-card {
    background:#fff; width:380px; padding:35px 30px;
    border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.25);
    text-align:center; position:relative;
}
.logo-login { font-size:70px; color:#d36b5e; margin-bottom:10px; }
h2 { margin:0 0 20px; color:#333; font-size:26px; }
.input-box { margin:15px 0; }
.input-box input {
    width:96%; padding:12px 14px; border-radius:8px; border:1px solid #ddd;
    background-color:#f2f2f2; color:#333; font-size:16px; outline:none;
}
.input-box input:focus { background-color:#ececec; border-color:#cfcfcf; box-shadow:none; }
.login-btn {
    width:100%; padding:12px; border:none; border-radius:8px;
    background:#b35d52; color:white; font-size:18px; margin-top:10px; cursor:pointer; font-weight:bold;
}
.login-btn:hover { background:#9e4f45; }
.row-rem_for { display:flex; justify-content:space-between; align-items:center; margin-top:15px; font-size:14px; width:100%; }
.forgot-link { color:#b35d52; text-decoration:none; }
.forgot-link:hover { text-decoration:underline; }
.register-link { margin-top:20px; font-size:14px; }
.register-link a { color:#b35d52; text-decoration:none; font-weight:bold; }
.register-link a:hover { text-decoration:underline; }
.error-msg { color:red; margin-bottom:10px; font-size:14px; }
<?php if($isPopup): ?>
.close-btn { position:absolute; top:12px; right:12px; font-size:20px; cursor:pointer; color:#444; }
<?php endif; ?>
</style>

<?php if (!$isPopup): ?>
</head>
<body>
<?php endif; ?>

<div class="overlay">
    <div class="login-card">
        <?php if($isPopup): ?>
        <div class="close-btn" onclick="
            document.getElementById('login-popup').style.display='none';
            document.getElementById('blur-overlay').style.display='none';
        ">✖</div>
        <?php endif; ?>

        <div class="logo-login"><i class="fa-solid fa-user-circle"></i></div>
        <h2>Login</h2>

        <?php if(!empty($login_error)): ?>
            <div class="error-msg"><?= $login_error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>

            <div class="row-rem_for">
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <a href="../login/forgot_password.html" class="forgot-link">Forgot Password?</a>
            </div>

            <p class="register-link">
                Don't have an account?
                <a href="../registration/registration.php">Register here</a>
            </p>
        </form>
    </div>
</div>

<?php if (!$isPopup): ?>
</body>
</html>
<?php endif; ?>

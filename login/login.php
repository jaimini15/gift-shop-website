<?php
session_start();

/* Detect if included inside another page */
$isPopup = isset($embedded) ? true : false;
?>

<?php if (!$isPopup): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Gift Shop</title>
<?php endif; ?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

/* When popup → blur overlay */
/* When full page → background plain */
.overlay {
    position: <?= $isPopup ? "fixed" : "relative" ?>;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    <?= $isPopup ? "backdrop-filter: blur(10px); background: rgba(0,0,0,0.3);" : "" ?>;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* LOGIN CARD */
.login-card {
    background: #fff;
    width: 380px;
    padding: 35px 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    text-align: center;
    position: relative;
}

.logo-login {
    font-size: 70px;
    color: #d36b5e;
}

h2 {
    margin: 0 0 20px;
    color: #333;
    font-size: 26px;
}

/* INPUT BOX */
.input-box {
    margin: 15px 0;
}

.input-box input {
    width: 96%;
    padding: 12px 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background-color: #f2f2f2 !important;
    color: #333;
    font-size: 16px;
    outline: none;
}

.input-box input:focus {
    background-color: #ececec !important;
    border-color: #cfcfcf !important;
    box-shadow: none !important;
}

/* Autofill Fix */
input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 30px #f2f2f2 inset !important;
    -webkit-text-fill-color: #333 !important;
}

/* BUTTON */
.login-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #b35d52;
    color: white;
    font-size: 18px;
    margin-top: 10px;
    cursor: pointer;
    font-weight: bold;
}

.login-btn:hover {
    background: #9e4f45;
}
/* remeber me + forget */
.row-rem_for {
    display: flex;
    justify-content: space-between; /* pushes items to left & right */
    align-items: center;            /* vertically center the checkbox & link */
    margin-top: 15px;
    font-size: 14px;
    width:100%;
    text-align: right;
}

.row-rem_for label {
    display: flex;
    align-items: center;
    gap: 5px; /* spacing between checkbox and text */
}

.forgot-link {
    color: #b35d52;
    text-decoration: none;
}

.forgot-link:hover {
    text-decoration: underline;
}



/* REGISTER LINK */
.register-link {
    margin-top: 20px;
    font-size: 14px;
}

.register-link a {
    color: #b35d52;
    text-decoration: none;
    font-weight: bold;
}

.register-link a:hover {
    text-decoration: underline;
}

/* CLOSE BUTTON ONLY IN POPUP */
<?php if ($isPopup): ?>
.close-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    font-size: 20px;
    cursor: pointer;
    color: #444;
}
<?php endif; ?>
</style>

<?php if (!$isPopup): ?>
</head>
<body>
<?php endif; ?>

<div class="overlay">

    <div class="login-card">

        <?php if ($isPopup): ?>
        <div class="close-btn" onclick="document.getElementById('login-popup').style.display='none';
                                       document.getElementById('blur-overlay').style.display='none';">
            ✖
        </div>
        <?php endif; ?>

        <div class="logo-login">
            <i class="fa-solid fa-user-circle"></i>
        </div>

        <h2> Login</h2>

        <form action="login_check.php" method="POST">

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>

          <div class="row-rem_for">
    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    <a href="../login/forgot_password.html" class="forgot-link">Forgot Password?</a>
</div>


            <p class="register-link">
                Don't have an account?
                <a href="../registration/registration.html">Register here</a>
            </p>

        </form>
    </div>
</div>

<?php if (!$isPopup): ?>
</body>
</html>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Shop - Login</title>

    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

<div class="login-card">

    <!-- User Icon -->
    <div class="logo">
    <i class="fa-solid fa-user-circle"></i>
</div>


    <h2>Login</h2>

    <form action="login.php" method="POST">

        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="login-btn">Login</button><br>

        <!-- Remember + Forgot row -->
        <div class="row">
            <label class="remember">
                <input type="checkbox" name="remember"> Remember Me
            </label>

            <a href="forgot_password.html" class="forgot-link">Forgot Password?</a>
        </div>

        <p class="register-link">
            Don't have an account?
            <a href="registration.html">Register here</a>
        </p>

    </form>

</div>

</body>
</html>

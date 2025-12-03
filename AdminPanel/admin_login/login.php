<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .login-box {
            width: 350px;
            background: #fff;
            padding: 25px;
            margin: 80px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            border: 0;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: -5px;
            margin-bottom: 10px;
        }

        .remember-label {
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap; /* THIS KEEPS "Remember Me" TOGETHER */
            gap: 6px;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 14px;
            text-decoration: none;
            color: #007bff;
            white-space: nowrap;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <form action="login_check.php" method="POST">

        <input type="email" 
               name="email" 
               placeholder="Admin Email"
               value="<?php echo isset($_COOKIE['admin_email']) ? $_COOKIE['admin_email'] : ''; ?>"
               required>

        <input type="password" name="password" placeholder="Password" required>

        <div class="options-row">

            <!-- Checkbox + Remember Me in one single line -->
            <label class="remember-label">
                <input type="checkbox" name="remember" 
                       <?php echo isset($_COOKIE['admin_email']) ? 'checked' : ''; ?>>
                Remember Me
            </label>

            <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <?php
        if(isset($_GET['error'])){
            echo "<p style='color:red; margin-top:10px;'>".$_GET['error']."</p>";
        }
    ?>
</div>

</body>
</html>

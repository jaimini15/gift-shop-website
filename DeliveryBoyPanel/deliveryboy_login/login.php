<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Boy Login</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .login-box {
            width: 350px;
            background: #fff;
            padding: 25px;
            margin: 80px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            font-size: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #2196F3;
            border: 0;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background: #0d89e9;
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
            white-space: nowrap;
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
    <h2>Delivery Boy Login</h2>

    <form action="login_check.php" method="POST">

        <input type="email"
               name="email"
               placeholder="Enter Email"
               value="<?php echo isset($_COOKIE['delivery_email']) ? $_COOKIE['delivery_email'] : ''; ?>"
               required>

        <input type="password" name="password" placeholder="Password" required>

        <div class="options-row">
            <label class="remember-label">
                <input type="checkbox" name="remember"
                    <?php echo isset($_COOKIE['delivery_email']) ? 'checked' : ''; ?>>
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

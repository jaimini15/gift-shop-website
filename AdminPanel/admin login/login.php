<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .login-box {
            width: 350px; background: #fff; padding: 25px;
            margin: 80px auto; border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input { width:100%; padding:10px; margin:10px 0; }
        button { width:100%; padding:10px; background:#4CAF50; border:0; color:#fff; font-size:16px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>
    <form action="login_check.php" method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <?php
        if(isset($_GET['error'])){
            echo "<p style='color:red'>".$_GET['error']."</p>";
        }
    ?>
</div>

</body>
</html>

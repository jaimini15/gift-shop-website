<?php
session_start();
include("../AdminPanel/db.php");

if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}
if (isset($_SESSION['User_Id'])) {
    $redirect = $_SESSION['redirect_after_login'] ?? '../home page/index.php';
    unset($_SESSION['redirect_after_login']);
    
    // Make sure relative paths for product pages
    if (!str_starts_with($redirect, '../') && !str_starts_with($redirect, '/')) {
        $redirect = '../product_page/' . $redirect;
    }
    
    header("Location: $redirect");
    exit();
}
$error = "";
$rememberedEmail = $_COOKIE['remember_email'] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $pass  = mysqli_real_escape_string($connection, $_POST['password']);

    $sql = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Use plain text password check 
        if ($pass === $row['Password']) {

            $_SESSION['User_Id'] = $row['User_Id'];
            $_SESSION['Email']   = $row['Email'];

            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }
            $redirect = $_SESSION['redirect_after_login'] ?? '../home page/index.php';
            unset($_SESSION['redirect_after_login']);

            if (!str_starts_with($redirect, '../') && !str_starts_with($redirect, '/')) {
                $redirect = '../product_page/' . $redirect;
            }

            header("Location: $redirect");
            exit();
        }
    }

    $error = "Invalid Email or Password!";
}
$rememberedEmail = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : "";
?>
<!DOCTYPE html>
<html>
<head>
     
    <title>Login</title>
    <link rel="stylesheet" href="../home page/style.css" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body { font-family: Arial; background: #f7f7f7; margin:0; }
        .form-box {
            width: 400px; margin: 30px auto; padding: 35px;
            background: #fff; border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        .btn {
            width: 100%; padding: 12px; background:#7e2626d5; 
            color: #fff; border: none; border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover { background: black;color:white; }
        .error { color: red; margin-bottom: 10px; }
        a { color: brown; text-decoration:none; }
        .extra-options {
            display: flex; justify-content: space-between;
            align-items: center; margin-top: 5px;
        }
        .extra-options label {
            display: flex; align-items:center;
            gap: 5px; cursor:pointer;
        }
        .login-icon {
    text-align: center;
    font-size: 70px;
    color: #7e2626d5;
    margin-bottom: 10px;
}

.login-title {
    text-align: center;
    margin: 0;
    font-size: 28px;
    font-weight: bold;
    color: #333;
}

    </style>
</head>
<body>

<?php include("../home page/navbar.php"); ?>  


<div class="form-box">

    <div class="login-icon">
        <i class="fa-solid fa-user-circle"></i>
    </div>

    <h2 class="login-title">Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required value="<?= $rememberedEmail ?>">

        <input type="password" name="password" placeholder="Password" required>

        <button class="btn">Login</button>

        <div class="extra-options">
            <label>
                <input type="checkbox" name="remember"> Remember me
            </label>

            <a href="forget_password_customer.php">Forgot Password?</a>
        </div>
    </form>
<br>
<center>
    <p>
        Don't have an account?
        <a href="../registration/registration.php">Register here</a>
    </p>
    </center>
</div>


<?php include("../home page/footer.php"); ?> 

</body>
</html>

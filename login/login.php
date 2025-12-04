<?php
session_start();
include("../AdminPanel/db.php");

// If already logged in redirect
if (isset($_SESSION['User_Id'])) {
    header("Location: ../home page/index.php");
    exit();
}

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if ($pass === $row['Password']) {

            // Set session
            $_SESSION['User_Id'] = $row['User_Id'];
            $_SESSION['Email']   = $row['Email'];

            // Remember Me (store email in cookie)
            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }

            header("Location: ../home page/index.php");
            exit();
        }
    }

    $error = "Invalid Email or Password!";
}

// Pre-fill email if "Remember Me" was used before
$rememberedEmail = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : "";
?>
<!DOCTYPE html>
<html>
<head>
     
    <title>Login</title>
    <link rel="stylesheet" href="../home page/style.css" />
    <style>
        body { font-family: Arial; background: #f7f7f7; margin:0; }
        .form-box {
            width: 350px; margin: 60px auto; padding: 25px;
            background: #fff; border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        .btn {
            width: 100%; padding: 12px; background: brown; 
            color: #fff; border: none; border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover { background: #8b3e3e; }
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
    </style>
</head>
<body>

<?php include("../home page/navbar.php"); ?>  <!-- Navbar -->

<div class="form-box">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required value="<?= $rememberedEmail ?>">

        <input type="password" name="password" placeholder="Password" required>

        <div class="extra-options">
            <label>
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <a href="../forgot_password/forgot.php">Forgot Password?</a>
        </div>

        <button class="btn">Login</button>
    </form>

    <p>
        Don't have an account? 
        <a href="../registration/registration.php">Register here</a>
    </p>
</div>

<?php include("../home page/footer.php"); ?> <!-- Footer -->

</body>
</html>

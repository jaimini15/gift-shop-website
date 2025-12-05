<?php
session_start();
include("../AdminPanel/db.php");

// If already logged in redirect
if (isset($_SESSION['User_Id'])) {
    header("Location: ../home page/index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if ($pass === $row['Password']) {
            $_SESSION['User_Id'] = $row['User_Id'];
            $_SESSION['Email']   = $row['Email'];

            header("Location: ../home page/index.php");
            exit();
        }
    }
    $error = "Invalid Email or Password!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; margin:0; }
        .form-box {
            width: 350px; margin: 60px auto; padding: 25px;
            background: #fff; border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
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
        a { color: brown; }
    </style>
</head>
<body>

<?php include("../home page/navbar.php"); ?>  <!-- 🔥 Navbar -->

<div class="form-box">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn">Login</button>
    </form>

    <p>
        Don't have an account? 
        <a href="../registration/registration.php">Register here</a>
    </p>
</div>

<?php include("../home page/footer.php"); ?> <!-- 🔥 Footer -->

</body>
</html>
